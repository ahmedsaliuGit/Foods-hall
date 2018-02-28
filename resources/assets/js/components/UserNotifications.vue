<template>
    <li class="dropdown" v-if="notifications.length">

        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-bell"></span>
        </a>

        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a :href="notification.data.link" v-text="notification.data.message" @click="markAsRead(notification)"></a>
            </li>
        </ul>
    </li>
</template>

<script>
export default {
    data() {
        return {notifications: false}
    },

    created() {
        axios.get("/profiles/"+window.App.user.name+"/notifications")
        .then(response => this.notifications = response.data);
    },

    methods: {
        // "profiles/{$user->name}/notification/".$user->unreadNotifications->first()->id
        markAsRead(notification) {
            axios.delete("/profiles/" + window.App.user.name + "/notification/" + notification.id)
                .then(window.location.reload());
        }
    }
}
</script>