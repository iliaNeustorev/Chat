<?php

namespace App\Http\Controllers;

use App\Events\HelpSupport;
use App\Events\SendMessage;
use App\Models\Censor;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->loginUsingId(3);
        $role = $user->roles->first()->name ?? 'Client';
        if($role == 'Lawyer' || $role == 'Manager') {
            $chatsClients = User::where('lawyer_id', $user->id)->orWhere('manager_id', $user->id)->get();
        }
        if($role == 'Support') {
            $clientIds = User::whereHas('roles', function (Builder $query) {
                $query->where('name', 'Client');
            })
                ->where([['lawyer_id', 0],['manager_id', 0]])
                ->pluck('id');
                $allChats = Channel::whereIn('owner_id', $clientIds)
                    ->whereHas('messages', function (Builder $query) use ($user) {
                        $query
                            ->latest()
                            ->limit(10)
                            ->where('sender_id', $user->id);
                    })
                    ->get()
                    ->map(function(Channel $channel){
                        preg_match('/\.(.*)/', $channel->hash, $matches);
                        $channel->hash = trim($matches[1]);
                        return $channel;
                    });
        }
        if($role == 'Admin') {
            $allChats = Channel::all();
        }
        return Inertia::render('Home', [
            'chatsClients' => $chatsClients ?? [],
            'allChats' => $allChats ?? [],
            'user' => $user,
            'role' => $role
        ]);
    }

    public function chat(User $client)
    {
        $user = Auth::user();
        $role = $user->roles->first()->name;
        $channel = Channel::where('owner_id', $client->id)->firstOrCreate(['owner_id' => $client->id], ['hash' => self::uniqueName($client->name)]);
        if($role == 'Client') {
            $recipient_id = $user->lawyer_id != 0 ? $user->lawyer_id : $user->manager_id;
            if($recipient_id == 0) {
                $recipient_id = null;
                $recipient_name = null;
            } else {
                $recipient_name = User::findOrFail($recipient_id)->name;
            }
        } else if($client->manager_id == $user->id || $client->lawyer_id == $user->id || $role == 'Support') {
            $recipient_id = $client->id;
            $recipient_name = $client->name;
        } else {
            $recipient_id = null;
            $recipient_name = null;
        }
        $allMessages = Message::select('messages.*', 'sender.name as sender_name', 'recipient.name as recipient_name')
            ->join('users AS sender', 'messages.sender_id', 'sender.id')
            ->leftjoin('users AS recipient', 'messages.recipient_id', 'recipient.id')
            ->where('channel_id', $channel->id)
            ->orderBy('created_at')
            ->get();
        if($user->id == $channel->owner_id || $client->lawyer_id == $user->id || $client->manager_id == $user->id || $role === "Admin") {
            return Inertia::render('Chat', [
                'chat' => (int)$channel->id, 
                'allMessages' => $allMessages ?? [],
                'user' => $user,
                'role' => $role, 
                'recipient' => $recipient_id,
                'recipient_name' => $recipient_name,
            ]);
        } elseif ($user->id != $channel->owner_id && $client->lawyer_id == 0 && $client->manager_id == 0 && $role === "Support") {
                return Inertia::render('Chat', [
                    'chat' => (int)$channel->id,
                    'allMessages' => $allMessages ?? [],
                    'user' => $user,
                    'role' => $role, 
                    'recipient' => $recipient_id,
                    'recipient_name' => $recipient_name,
                ]);
        }
        return Inertia::render('403');
    }

    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required',
        ]);
        $user = Auth::user();
        $role = $user->roles->first()->name;
        $data = $request->all() + ['sender_id' => $user->id];
        if($role == 'Client' && $user->manager_id == 0 && $user->lawyer_id == 0) {
            $channel = Channel::where('owner_id', $user->id)->firstOrfail();
            $lastFiveRecord = $channel->messages()->latest()->limit(5)->get();
            $checkSupport = $lastFiveRecord->where('sender_id', '!=', $user->id)->count();
            if($checkSupport == 0){
                broadcast(new HelpSupport($channel))->toOthers();
            }
        }
        if($role == 'Support'){
            $recipient = User::findOrfail($data['recipient_id']);
            $roleRecipient = $recipient->roles->first()->name;
            if($roleRecipient == 'Client' && $recipient->manager_id == 0 && $recipient->lawyer_id == 0) {
                $supports = User::whereHas('roles', function (Builder $query) {
                    $query->where('name', 'Support');
                })->whereNotIn('id', [$user->id])->pluck('id');
                $checkSupport = Message::where('channel_id', $data['channel_id'])
                    ->select('id', 'channel_id', 'sender_id')
                    ->latest()
                    ->limit(5)
                    ->whereIn('sender_id', $supports)
                    ->count();
                if($checkSupport != 0) {
                   return 'Пользователь уже общается с поддержкой';
                }
            }
        }
        $role = $user->roles->first()->name;
        $data['status'] = $role == 'Client' ? 'Ок' : self::checkCensor($data['text']);
        $message = Message::create($data);
        broadcast(new SendMessage($data, $message->id))->toOthers();
        return Redirect::back();
    }

    public function getMessagesForModerate()
    {
        $messages = Message::select('messages.*', 'sender.name AS sender_name', 'role.name AS role')
        ->join('users AS sender', 'messages.sender_id', 'sender.id')
        ->join('role_user', 'messages.sender_id', 'role_user.user_id')
        ->join('roles AS role', 'role_user.role_id', 'role.id')
        ->where('status', 'На модерацию')
        ->where('role.name', '!=', 'Client')
        ->orderBy('created_at')
        ->get();
        return Inertia::render('Messages', ['messages' => $messages]);
    }

    public function messageModerate(Request $request, Message $message)
    {
        $data = $request->all();
        $message->update(['status' => $data['status']]);
        return Redirect::back();
    }

    public function addWordToCensor(Request $request)
    {
        $data = $request->all();
        Censor::create($data);
        return Redirect::back();
    }

    public function update(Request $request, Message $message)
    {
        $user = Auth::user();
        $data = $request->all();
        $role = $user->roles->first()->name;
        $currentDate = Carbon::now();
        if($user->id != $message->sender_id){
            return Inertia::render('403');
        }
        if($message->created_at->diffInHours($currentDate) >= 1) {
            return Inertia::render('403');
        }
        $data['status'] = $role == 'Client' ? 'Ок' : self::checkCensor($data['text']);
        $message->update($data);
        broadcast(new SendMessage($data, $message->id, true))->toOthers();
        return Redirect::back();
        
    }

    protected function checkCensor(string $text)
    {
        $illegalWords = Censor::pluck('word');
        $status = 'Ok';
        foreach($illegalWords as $word){
            if(preg_match("/$word/ui", $text)){
               $status = 'На модерацию';
            }
        }
        return $status;
    }

    protected function uniqueName($str)
    {
        $hash = self::fileHashName($str, 16);
        $channelsHashName = Channel::pluck('hash')->toArray();
        while (in_array($hash, $channelsHashName)) {
            $hash = self::fileHashName($str, 16);
        }
        return $hash;
    }

    protected function fileHashName($str, $symbol = 40)
    {
        $str = explode('.', $str);
        return Str::random($symbol) . '.' . end($str);
    }
}
