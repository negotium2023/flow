<template>
    <div>
        <div class="loader align-items-center" v-if="loading">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </div>
        <div class="card" v-if="!loading">
            <div  class="card-header">
                Client Basket
            </div>

            <div class="card-body col-sm-12">
                <nav class="tabbable">
                    <div class="nav nav-pills">
                        <a v-if="displayActivities > 0"
                           class="nav-link" id="activities-tab"
                           :class="{active: displayActivities > 0}"
                           data-toggle="tab" href="#activities" role="tab" aria-controls="default" aria-selected="false">Activities</a>
                        <a v-if="displayInputs > 0"
                           class="nav-link"
                           :class="{active: displayActivities < 1}"
                           id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="default" aria-selected="false">Details</a>
                    </div>
                </nav>
                <div class="tab-content" id="myTabContent">
                    <div v-if="displayActivities > 0" class="tab-pane fade show active p-3" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                        <attooh-accordion :items="activities"></attooh-accordion>
                    </div>
                    <div v-if="displayInputs > 0" class="tab-pane fade p-3" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <attooh-accordion :items="inputs"></attooh-accordion>
                    </div>
                </div>
                <div v-if="!(displayActivities > 0) && !(displayInputs > 0)" class="text-center">
                    <p>Nothing to show</p>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import AttohAccordion from './attooh-accordion';
    export default {
        data: function(){
            return {
                loading: true,
                activities: [],
                displayActivities:[],
                inputs: [],
                displayInputs: [],
            }
        },
        components: {
            'attooh-accordion': AttohAccordion
        },
        mounted() {
            this.getActivities()
        },
        methods: {
            getActivities: function () {
                let current_url = window.location.pathname.split('/');

                axios.get('/client/'+current_url[2]+'/'+current_url[4]+'/clientbasket')
                    .then(response => {
                        this.loading = false;
                        this.activities = response.data.client_basket_activities;
                        response.data.client_basket_activities.map(activ => {
                            this.displayActivities.push(activ.body.length)
                        });
                        this.displayActivities = this.displayActivities.filter(item => item > 0).length;
                        console.log("Activities" + this.displayActivities)
                        this.inputs = response.data.client_basket_details;
                        response.data.client_basket_details.map(activ => {
                            this.displayInputs.push(activ.body.length)
                        });
                        this.displayInputs = this.displayInputs.filter(item => item > 0).length;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            }
        }
    }

</script>

<style scoped>
    /********************************************************
       ****************** CSS LOADDER **************************
       ********************************************************/
    .loader{
        height: 16rem;
        display: flex;
        align-items: center;
    }
    .lds-ring {
        display: block;
        position: relative;
        width: 80px;
        height: 80px;
        margin-left: auto;
        margin-right: auto;
    }
    .lds-ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        width: 64px;
        height: 64px;
        margin: 8px;
        border: 8px solid #4c5e77;
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #4c5e77 transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
        animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
        animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
        animation-delay: -0.15s;
    }
    @keyframes lds-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>