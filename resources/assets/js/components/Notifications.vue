<template>
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" @click="clearNotifications">
            <i style="color: red;" class="fa fa-bell" v-if="hasUnread"></i>
            <i id="notificatins-icon" class="far fa-bell" v-else></i>
            <span class="badge badge-pill badge-danger" v-if="hasUnread">{{ counter }}</span>
        </a>
        <div class="dropdown-menu blackboard-notification-dropdown dropdown-menu-right">
            <a class="dropdown-item" v-for="notification in notifications" v-on:click="MarkAsRead(notification)">
                <div class="media">
                    <!--<img v-bind:src="getPic(message)" class="img-circle elevation-2" alt="User Image">-->
                    <div class="media-body">
                        <p class="text-sm">{{notification.name}}</p>
                        <p class="text-sm text-muted float-right"><i class="fa fa-clock mr-1"></i> {{notification.created}}</p>
                    </div>
                </div>
            </a>
            <a class="dropdown-item" v-if="notifications.length==0">No notifications yet!</a>
            <a v-on:click="goto_route()" class="dropdown-item" style="text-align:left;text-decoration: none;font-size:12px;cursor:pointer;">View Notification History</a>
        </div>
    </li>
</template>

<script>
    export default {
        props: {
            blackUser: {
                default: ''
            },
        },
        data() {
            return {
                hasUnread: false,
                notifications: []
            }
        },
        methods: {
            goto_route: function () {
                var route = "/notifications";
                location.href = route;
            },
            clearNotifications: function () {
                this.hasUnread = false;
                axios.post('/markallnotifications').then(response => {
                    console.log('success');
                });
            },
            ListenForActivity: function (){
                Echo.private(`notifications.${this.blackUser}`)
                    .listen('NotificationEvent', (e) => {
                        console.log(`${this.blackUser}`);
                        this.hasUnread = true;
                        this.GetCount();
                    });
            },
            MarkAsRead: function(notification){
                var data = {
                    id: notification.id
                };

                axios.post('/readnotifications',data).then(response => {
                    window.location.href = notification.link;
                });
            },
            GetCount: function(){
                axios.post('/getnotificationscount')
                    .then(response => {
                        if(response.data.count > 0){

                            this.hasUnread = true;
                        } else {
                            this.hasUnread = false;
                        }//
                        //
                        this.counter = response.data.count;
                        this.notifications = response.data.notifications;
                    })
                    .catch(error => {
                        // todo handle error
                    });
            }
        },
        created() {
            this.GetCount();
            this.ListenForActivity();

        }
    }
</script>
