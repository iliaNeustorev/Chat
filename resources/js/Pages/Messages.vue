<template>
    <div v-for="message in messages">
        <p>{{ message.text }}</p>
        <p>{{ message.sender_name }}</p>
        <p>{{ message.role }}</p>
        <button
            class="btn btn-primary"
            @click="sendSolution('accept', message.id)"
        >
            Понятно
        </button>
    </div>
</template>
<script>
export default {
    props: {
        messages: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                status: "",
            }),
        };
    },
    methods: {
        sendSolution(solution, id) {
            if (solution == "accept") {
                this.form.status = "Ок";
            }
            this.form.put(`/moderate/${id}`);
        },
    },
};
</script>
