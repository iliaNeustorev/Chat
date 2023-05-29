<template>
    <div>
        <div class="users">
            <h1>Пользователи в чате</h1>
            <div v-for="user in activeUsers">
                <span class="user"
                    >{{ user.name
                    }}<span class="role" v-if="user.role != 'Client'"
                        >-{{ user.role }}</span
                    ></span
                >
            </div>
        </div>
        <hr />
        <h1 class="mb-3 users">Сообщения от пользователей</h1>
        <hr />
        <div v-for="item in allMessages" :key="item.id">
            <hr />
            <p>{{ item.text }}</p>
            <p>{{ item.created_at }}</p>
            <p>{{ item.status }}</p>
            <hr />
        </div>
        <hr />
    </div>
    <form @submit.prevent="submitMessage">
        <div class="col-md-6">
            <textarea @keydown="actionUser" v-model="form.text"></textarea>
            <hr />
            <span v-if="isActive"
                >{{ isActive.name }} набирает сообщение...</span
            >
        </div>
        <button class="btn btn-primary">Отправить</button>
    </form>
    <a href="/"><button class="btn btn-primary">Назад</button></a>
    {{ user.name }}
</template>
<script>
export default {
    props: {
        chat: { type: Number, required: true },
        allMessages: { type: Object, required: false },
        user: { type: Object },
    },
    data() {
        return {
            messages: [],
            isActive: false,
            typingTimer: false,
            activeUsers: [],
            form: this.$inertia.form({
                text: "",
                channel_id: this.chat,
                recipient_id: 2,
            }),
        };
    },
    computed: {
        channel() {
            return Echo.join(`translation.${this.chat}`);
        },
    },
    created() {
        this.channel
            .here((users) => {
                this.activeUsers = users;
            })
            .joining((user) => this.activeUsers.push(user))
            .leaving((user) =>
                this.activeUsers.splice(this.activeUsers.indexOf(user), 1)
            )
            .listen(".new-message", (e) => {
                let message = {
                    id: e.id,
                    text: e.message,
                    status: "Новое сообщение",
                };
                // console.log(e);
                console.log(message);
                setTimeout(() => {
                    this.allMessages.push(message);
                }, 700);
                this.isActive = false;
            })
            .listenForWhisper("typing", (e) => {
                this.isActive = e;
                if (this.typingTimer) clearTimeout(this.typingTimer);
                this.typingTimer = setTimeout(() => {
                    this.isActive = false;
                }, 2000);
            });
    },
    methods: {
        submitMessage() {
            this.form
                .post(`/chat/message`)
                .then((response) => {})
                .catch((error) => {});
        },
        actionUser() {
            this.channel.whisper("typing", { name: this.user.name });
        },
    },
    mounted() {
        console.log(this.allMessages);
    },
};
</script>

<style scoped>
.users {
    color: green;
}
.user {
    color: steelblue;
}
.role {
    color: rgb(69, 102, 19);
}
</style>
