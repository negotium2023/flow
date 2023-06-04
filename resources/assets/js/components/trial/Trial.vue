<template>
    <div>
        <div class="trial-notification text-center" v-if="display_trial && role">
            Your Trial end on {{expiry_date}}, you can upgrade to full package<span v-html="link"></span>
            <span class="float-right mr-2"  @click="dismissTrial" v-if="role">X</span>
        </div>
        <div class="trial-notification text-center" v-if="display_trial && !role">
            Your Trial end on {{expiry_date}}, Please ensure the Primary User subscribes from his/her screen
        </div>
    </div>
</template>

<script>
    export default {
        data: function(){
            return {
                display_trial: false,
                expiry_date: '',
                role: false,
                link: ''
            }
        },
        mounted() {
            axios.get('/trial')
                .then(response => {
                    this.display_trial = response.data.trial;
                    this.expiry_date = response.data.expiry_date;
                    this.role = response.data.role
                    if (response.data.role){
                        this.link = "<a href='http://helpdesk.blackboardbs.com/' style='color:#ffffff'>here.</a>"
                    }
                    //console.log(response);
                })
                .catch(error => {
                    // handle error
                    //console.log(error);
                });
        },
        methods: {
            dismissTrial: function(){
                this.display_trial = !this.display_trial;
                axios.post('/dismiss-trial', {
                    is_dismissed: 1,
                })
                    .then(function (response) {
                        if (response.data.status === 'success'){
                            this.display_trial = false;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }
    }
</script>

<style scoped>
    .trial-notification{
        padding: 0.6rem 0;
        font-weight: bold;
        color: #ffffff;
        background: linear-gradient(-45deg, #3eb4d3, #46c8ea, #3eb4d3, #46c8ea );
        background-size: 400% 400%;
        -webkit-animation: gradientBG 10s ease infinite;
        animation: gradientBG 10s ease infinite;
    }
    .trial-notification>span{
        padding:0 0.5rem;
        cursor: pointer;
    }
    .trial-notification>span>a{
        color: #ffffff!important;
    }
</style>