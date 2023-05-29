<template>
    <div>
        {{ user.name }}
        <hr />
        Чаты с клиентами
        <div v-for="chat in chatsClients" :key="chat.id">
            <a :href="`/chat/${chat.id}`"
                ><button class="btn btn-primary">
                    Чат {{ chat.hash }}
                    <span v-if="chat.newCount">({{ chat.newCount }})</span>
                </button></a
            >
            <input
                v-if="role != 'Client'"
                type="checkbox"
                v-model="isChecked[chat.id]"
                @change="toggleListenChat(chat.id)"
            />
        </div>
        <hr />
        <div v-if="role != 'Client'">
            Личные чаты
            <div v-for="chat in chatsPrivate" :key="chat.id">
                <a :href="`/chat/${chat.id}`"
                    ><button class="btn btn-primary">
                        Чат {{ chat.hash }}
                        <span v-if="chat.newCount">({{ chat.newCount }})</span>
                    </button></a
                >
            </div>
            <form @submit.prevent="beginChat">
                <button class="btn btn-primary">Начать чат</button>
            </form>
        </div>

        <hr />
    </div>
</template>
<script>
export default {
    props: {
        chatsClients: Object,
        chatsPrivate: Object,
        user: Object,
        role: String,
    },
    data() {
        return {
            listenChats: [],
            isChecked: {},
            form: this.$inertia.form({}),
        };
    },
    methods: {
        beginChat() {
            this.form
                .post(`/new-chat/${1}`)
                .then((response) => {})
                .catch((error) => {});
        },
        toggleListenChat(id) {
            if (this.isChecked[id]) {
                this.listenChats.push(id);
                this.listenChannel();
            } else {
                const index = this.listenChats.indexOf(id);
                this.listenChats.splice(index, 1);
                console.log(id);
                this.leaveChannel(id);
            }
        },
        listenChannel() {
            this.listenChats.forEach((chat) => {
                Echo.join(`translation.${chat}`).listen(".new-message", (e) => {
                    let currentChat = this.chatsClients.find(
                        (item) => item.id == chat
                    );
                    if (currentChat.newCount == null) {
                        currentChat.newCount = 1;
                    } else {
                        currentChat.newCount++;
                    }
                });
            });
        },
        leaveChannel(chat) {
            Echo.leave(`translation.${chat}`);
        },
        // deleteMessage(messageId) {
        //     this.updating = true;
        //     axios
        //         .delete(`/api/messages/${messageId}/deleteMessage`)
        //         .then(() => {
        //             this.getResults(this.page);
        //         })
        //         .finally(() => {
        //             this.updating = false;
        //         });
        // },
        // changeMessage(messageId) {
        //     this.changeFlag = true;
        //     this.messageId = messageId;
        // },
    },
    mounted() {
        this.listenChannel();
        // this.chatsPrivate.forEach((chat) => {
        //     Echo.private(`translation.${chat.id}`).listen(
        //         ".new-message",
        //         (e) => {
        //             if (chat.newCount == null) {
        //                 chat.newCount = 1;
        //             } else {
        //                 chat.newCount++;
        //             }
        //         }
        //     );
        // });
    },
};
</script>
