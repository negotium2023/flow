<template>
    <div>
        <div class="card" v-for="(item, index) in customItems">
            <div class="card-header" @click="activateBody(index)" v-if="item.body.length > 0">
                {{item.title}}
                <span class="float-right" :class="{ rotate: item.rotateOnClick, 'rotate-reverse': !item.rotateOnClick }">
                    <i class="fas fa-arrow-down"></i>
                </span>
            </div>
            <transition name="slide-fade">
                <div class="card-body" v-if="item.status">
                    <ul>
                        <li v-for="active in item.body"
                            :class="{
                                'list-item': active.header,
                                 'sub-heading': active.subHeader,
                                 'not-heading': !active.header && !active.subHeader
                            }"
                        >{{active.name}}</li>
                    </ul>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
    export default {
        props:["items"],
        data: function(){
            return {
                collapsibleItems: [],
            }
        },
        computed: {
            customItems(){
                this.collapsibleItems = this.items.map(item => {
                    return {
                        title:item.title,
                        body: item.body,
                        status: false,
                        rotateOnClick: false
                    }
                })
                return this.collapsibleItems;
            }
        },
        methods: {
            activateBody: function (index) {
                this.collapsibleItems[index].status = !this.collapsibleItems[index].status;
                this.collapsibleItems[index].rotateOnClick = !this.collapsibleItems[index].rotateOnClick;
                this.collapsibleItems.forEach((item, ind) =>{
                    if(ind != index){
                        item.status = false
                        item.rotateOnClick = false
                    }
                })
            }
        }
    }
</script>

<style scoped>
    .card-header{
        cursor: pointer!important;
    }
    .slide-fade-enter-active{
        transition: all 0.6s ease;
    }
    .slide-fade-enter{
        transform: translate(5rem);
        opacity: 0;
    }
    ul{
        padding: 0 2rem;
        list-style: none;
        max-height: 250px;
        overflow-y: auto;
    }
    ul li{
        background-color: rgba(70, 200, 234, 0.6);
        padding: 5px 10px;
        margin-bottom: 5px;
        border-left: 3px solid #46C8EA;
    }
    .not-heading{
        background-color: rgba(70, 200, 234, 0.08);
    }
    .sub-heading{
        background-color: rgba(70, 200, 234, 0.1);
    }
    .heading{
        background-color: rgba(70, 200, 234, 0.5);

    }
    .card{
        margin-bottom: 5px!important;
    }
    .rotate{
        transition: all 0.4s linear;
        transform: rotate(-180deg);
        color: #46C8EA;
    }
    .rotate-reverse{
        transition: all 0.4s linear;
        transform: rotate(0deg);
        color: #46C8EA;
    }
</style>