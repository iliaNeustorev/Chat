<template>
    <div>
        <hr />
        <h1 class="mb-3 users">Сообщения от пользователей</h1>
        <hr />
        <div v-for="item in allMessages" :key="item.id">
            <hr />
            <p v-if="item.parent_id != null" class="opac">
                <span class="answer">Ответ на </span
                >{{ findParent(item.parent_id).text }}
            </p>
            <p
                @dblclick="showEditMessage(item.id, item.text)"
                @click="setParent(item.id)"
            >
                {{ item.text }}
            </p>
            <p>{{ item.created_at }}</p>
            <p
                v-if="
                    item.status == 'Новое сообщение' ||
                    item.status == 'Сообщение изменено'
                "
            >
                {{ item.status }}
            </p>
            <p class="users">
                <span class="name">
                    <span class="newMessages">Отправлено -</span>
                    <span>{{ item.sender_name }}</span></span
                >

                <span>
                    <span class="newMessages"> Кому - </span
                    >{{ item.recipient_name ?? "поддержка клиента" }}</span
                >
            </p>
            <hr />
        </div>
        <hr />
    </div>
    <form v-if="role != 'Admin'" @submit.prevent="submitMessage">
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
    <form v-if="showEdit" @submit="updateMessage">
        <div class="col-md-6">
            <textarea v-model="edit.text"></textarea>
            <button class="btn btn-primary">Изменить</button>
        </div>
    </form>
</template>
<script>
export default {
    props: {
        chat: { type: Number, required: true },
        allMessages: { type: Object, required: false },
        user: { type: Object },
        role: { type: String },
        recipient: { type: Number, required: false },
        recipient_name: { type: String },
    },
    data() {
        return {
            showEdit: false,
            messageId: null,
            newMessages: [],
            isActive: false,
            typingTimer: false,
            form: this.$inertia.form({
                text: "",
                channel_id: this.chat,
                recipient_id: this.recipient,
                recipient_name: this.recipient_name,
                sender_name: this.user.name,
                parent_id: null,
            }),
            edit: this.$inertia.form({
                text: "",
                channel_id: this.chat,
                recipient_id: this.recipient,
                recipient_name: this.recipient_name,
                sender_name: this.user.name,
            }),
        };
    },
    computed: {
        channel() {
            return Echo.private(`translation.${this.chat}`);
        },
        findParent(id) {
            return (id) => this.allMessages.find((item) => item.id == id);
        },
    },
    created() {
        this.channel
            .listen(".new-message", (e) => {
                let message = {
                    id: e.id,
                    text: e.message,
                    status: "Новое сообщение",
                    sender_name: e.sender_name,
                    recipient_name: e.recipient_name,
                    parent_id: e.parent_id,
                };
                console.log(message);
                this.allMessages.push(message);
                this.isActive = false;
            })
            .listen(".change-message", (e) => {
                console.log(e);
                let i = this.allMessages.find((item) => item.id == e.id);
                i.status = "Сообщение изменено";
                i.text = e.message;
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
                .post(`/chat/message`, {
                    preserveScroll: true,
                    onSuccess: () => this.form.reset(),
                })
                .catch((error) => {});
        },
        showEditMessage(id, text) {
            this.showEdit = !this.showEdit;
            this.edit.text = text;
            this.messageId = id;
        },
        updateMessage() {
            this.edit.put(`/chat/message/edit/${this.messageId}`, {
                preserveScroll: true,
            });
        },
        setParent(id) {
            this.form.parent_id = id;
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
.newMessages {
    color: blueviolet;
}
.user {
    color: steelblue;
}
.role {
    color: rgb(69, 102, 19);
}
.name {
    color: brown;
}
.answer {
    color: rgb(97, 121, 50);
}
.opac {
    opacity: 50%;
}
</style>
