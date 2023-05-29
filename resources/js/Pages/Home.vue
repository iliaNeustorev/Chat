<template>
    <div>
        {{ user.name }}
        <hr />
        <div v-if="role == 'Support'">
            Монитор чатов с пользователями
            <div v-for="chat in realTimeChannels">
                <a :href="`/chat/${chat.owner_id}`"
                    ><button class="btn btn-primary">
                        Чат {{ chat.hash }}
                        <span v-if="chat.newCount">({{ chat.newCount }})</span>
                    </button></a
                >
            </div>
        </div>
        <div v-if="role != 'Client' && role != 'Admin'">
            Чаты с клиентами
            <div v-for="client in chatsClients" :key="client.id">
                <a :href="`/chat/${client.id}`"
                    ><button class="btn btn-primary">
                        Чат {{ client.name }}
                        <!-- <span v-if="user.newCount">({{ client.newCount }})</span> -->
                    </button></a
                >
                <!-- <input
                v-if="role != 'Client'"
                type="checkbox"
                v-model="isChecked[chat.id]"
                @change="toggleListenChat(chat.id)"
            /> -->
            </div>
        </div>
        <div v-if="role == 'Admin' || role == 'Support'">
            <div v-for="chat in allChats">
                <a :href="`/chat/${chat.owner_id}`"
                    ><button class="btn btn-primary">
                        Чат {{ chat.hash }}
                        <!-- <span v-if="user.newCount">({{ client.newCount }})</span> -->
                    </button></a
                >
            </div>
        </div>
        <hr />
        <div v-if="role == 'Client'">
            <div>
                <a :href="`/chat/${user.id}`"
                    ><button class="btn btn-primary">Начать чат</button></a
                >
            </div>
        </div>
        <hr />
        <div v-if="role == 'Admin'">
            <form @submit.prevent="sendCensor">
                <div class="col-md-6">
                    <input
                        class="input"
                        v-model="censor.word"
                        placeholder="Ввведите слово"
                    />
                </div>
                <button class="btn btn-primary">Принять</button>
            </form>
            <a :href="'/messages-moderate'">Сообщения для модерации</a>
        </div>
        <hr />
    </div>
</template>
<script>
export default {
    props: {
        chatsClients: Object,
        allChats: { type: Object, required: false },
        user: Object,
        role: String,
        clientsWithoutChats: { type: Object, required: false },
    },
    data() {
        return {
            realTimeChannels: [],
            listenChats: [],
            isChecked: {},
            form: this.$inertia.form({}),
            censor: this.$inertia.form({
                word: "",
            }),
        };
    },
    methods: {
        beginChat(id) {
            this.$inertia.get(`/chat/${id}`);
        },
        sendCensor() {
            this.censor.post("/new-censor");
        },
        toggleListenChat(id) {
            if (this.isChecked[id]) {
                this.listenChats.push(id);
                this.listenChannel();
            } else {
                const index = this.listenChats.indexOf(id);
                this.listenChats.splice(index, 1);
                this.leaveChannel(id);
            }
        },
    },
    created() {
        Echo.private(`channel-support`).listen(".new-request", (e) => {
            let i = this.realTimeChannels.find((item) => item.id == e.id);
            console.log(i);
            if (i == undefined) {
                this.realTimeChannels.push(e);
            } else {
                if (i.newCount == undefined) {
                    i.newCount = 1;
                } else {
                    i.newCount++;
                }
            }
        });
    },
};
</script>
