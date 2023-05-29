<template>{{ text }}</template>
<script>
export default {
    props: { role: String },
    computed: {
        text() {
            return this.role == "Admin"
                ? "Нет прав на редактирования"
                : "Нет прав доступа к чату";
        },
    },
};
</script>
