<template>
    <div class="view-card elevation-4" style="width: 800px;">
        <div class="card-header">
            <p class="text-muted mb-0">{{cardDetails.section_name}}</p>
            <input type="text"
                   v-model="card_form.name"
                   v-on:keyup.enter="updateCard({card_id: cardDetails.id, name: card_form.name})">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    Assignee<br>
                    <select class="form-control" v-model="card_form.assignee_id" v-on:change="updateCard({card_id: cardDetails.id, assignee_id: card_form.assignee_id})">
                        <option value="" disabled>Select Assignee</option>
                        <option v-for="user in office_users" :value="user.id">{{user.first_name}} {{user.last_name}}</option>
                    </select>
                </div>
                <div class="col-md-8">
                    Assign Team members
                    <!--<v-select v-model="card_form.team_ids" :from="office_users" />-->
                    <multiselect v-model="selectedObjects" deselect-label="Can't remove this value" track-by="id" label="first_name" placeholder="Select one" :options="office_users"  @focusout="updateCard({card_id: cardDetails.id, team_ids: card_form.team_ids})":searchable="false" :allow-empty="false" :multiple="true">
                    </multiselect>
                    <!--<select class="form-control col-md-8" v-model="card_form.team_ids" multiple="true">
                      <option disabled>Select Team Members</option>
                      <option v-for="user in office_users" :value="user.id">{{user.first_name}} {{user.last_name}}</option>
                    </select>-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    Due Date<br>
                    <DueDate
                            @pick="updateCardDuedate"
                            @blur="cardID(cardDetails.id)"
                            v-model="card_form.due_date"
                            type="format"
                            :placeholder="card_form.due_date"
                    ></DueDate>
                </div>
                <div class="col-md-4">
                    Status
                    <select v-model="card_form.progress_status_id" @change="updateCard({card_id: cardDetails.id, status_id: card_form.progress_status_id})" class="form-control">
                        <option disabled>Select Priority</option>
                        <option v-for="status in progress_status" :value="status.id">{{status.name}}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    Priority
                    <select v-model="card_form.priority_status_id" @change="updateCard({card_id: cardDetails.id, priority_id: card_form.priority_status_id})" class="form-control">
                        <option disabled>Select Priority</option>
                        <option v-for="status in priority_status" :value="status.id">{{status.name}}</option>
                    </select>
                </div>
            </div>
            <div class="row">
                Description
                <textarea v-model="card_form.description" @focusout="updateCard({card_id: cardDetails.id, description: card_form.description})"  rows="3" class="form-control">{{card_form.description}}</textarea>
            </div>
            <p><strong>Tasks</strong><a href="javascript:void(0)" class="float-right" @click="addTask"><i class="fa fa-plus"></i> Add task</a></p>
            <div class="col template-tasks">
                <div>
                    <div class="row" v-show="add_task">
                        <div class="col-md-12">
                            <p><span><input type="checkbox" ></span> <strong><input type="text" v-on:keyup.enter="saveTask(cardDetails.id)" v-model="task.name" class="add-task form-control" /></strong>

                            </p>
                        </div>
                        <div class="col-md-6 py-0"> Assign person</div>
                        <div class="col-md-6 py-0 text-right">
                            <span class="mr-2">Add deadline</span><span>Add subtasks</span>
                        </div>
                    </div>

                    <!--   Assign user to a task     -->
                    <div v-show="assign_user">
                        <p class="text-muted">
                            Task Assignee
                        </p>
                        <select v-model="task_assignee_id" class="form-control">
                            <option disabled>Select User</option>
                            <option v-for="user in office_users" :value="user.id">{{user.first_name+" "+user.last_name}}</option>
                        </select>
                        <button @click="saveAssignedUser" type="button" class="btn btn-sm btn-primary">Assign</button>
                    </div>

                    <!--    Add deadline to a task     -->

                    <div v-show="add_deadline">
                        <strong>{{task_update.name}}</strong><br>
                        <DueDate v-model="deadline" type="timestamp"></DueDate>
                        <button @click="saveDeadline" type="button" class="btn btn-sm btn-primary">Add Deadline</button>
                    </div>

                    <!--    Add subtask to a task     -->

                    <div v-show="add_subtask">
                        <p class="text-muted">
                            Subtask name goes here
                        </p>
                        <input type="text" v-model="subtask_name" v-on:keyup.enter="saveSubtask">
                        <span class="float-right" @click="finishAddingSubtasks">Finished</span>
                    </div>

                    <!--    Update task name    -->
                    <div v-show="task_name_update">
                        <input type="text" v-model="task_update.name" @keyup.enter="updateTask({task_id: task_update.id, name:task_update.name})">
                    </div>

                    <!--    View subtasks    -->

                    <div v-show="view_subtasks">
                        <div><Strong>{{subtasks.name}}</Strong></div>
                        <div class="row my-0 py-0">
                            <div class="col-md-6">{{subtasks.assignee}}</div>
                            <div class="col-md-6 text-right">
                                <span>{{subtasks.due_date}}</span>
                                <span class="ml-2">{{subtasks.subtasks_count}} Subtasks</span>
                            </div>
                            <div>
                                <ul>
                                    <li v-for="subtask in subtasks.subtasks"  @click="initUpdateTask(subtask)">{{subtask.name}}</li>
                                </ul>

                            </div><br>
                        </div>
                    </div>

                    <!--    List of tasks    -->
                    <div v-for="task in card_tasks" class="border-bottom row">
                        <div class="col-md-4">
                            <p><span><input type="checkbox"  :value="task.status_id"></span>
                                <strong @click="initUpdateTask(task)">{{task.name}}</strong><br>
                                <span @click="assignUser(task.id)">{{task.assigned.first_name? task.assigned.first_name : ""}} {{task.assigned.last_name?task.assigned.last_name:""}}</span>
                            </p>
                        </div>

                        <div class="col-md-4">
                            <span v-if="task.due_date" @click="addDeadline(task)">{{task.due_date}}</span>
                            <span v-if="!task.due_date" @click="addDeadline(task)">Add deadline</span>
                        </div>
                        <div class="col-md-4">
                            <span v-if="task.sub_tasks_count > 0" @click="viewSubtasks(task)">{{task.sub_tasks_count?task.sub_tasks_count:0}} Subtasks</span>
                            <span v-if="task.sub_tasks_count < 1" @click="addSubtask(task.id)">Add Subtasks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            Discussion
            <input type="text" v-model="discussion_message" class="form-control">
            <button type="button" @click="saveDiscussionMessage(cardDetails.id)" class="btn btn-sm btn-primary">Post</button>
            <div v-for="discussion in discussions">
                <strong>{{ (discussion.user.first_name?discussion.user.first_name:"") + " " + (discussion.user.last_name?discussion.user.last_name:"")}}</strong>
                <p>{{discussion.message}}</p>
                <hr>
            </div>
            <hr>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';


    export default {
        name: "Card.vue",
        props: ["cardDetails"],
        components:{
            "DueDate": DatePicker,
            "multiselect": Multiselect
        },
        mounted(){
            this.getOfficeUsers();
            this.getCardStatus();
        },
        watch: {
            'cardDetails': function (val, oldVal) {
                this.card_form.name = val.name;
                this.card_form.assignee_id = val.assignee_id;
                this.card_form.team_ids = val.team_ids ? val.team_ids.split(", ") : [];
                this.card_form.due_date = val.due_date;
                this.card_form.priority_status_id = val.priority_id;
                this.card_form.progress_status_id = val.status_id;
                this.card_form.description = val.description;
                this.card_tasks = val.tasks;
                this.discussions = val.discussions;
            },
            selectedObjects(newValues) {
                this.card_form.team_ids = newValues.map(obj => obj.id);
            console.log(this.card_form.team_ids)
        },
        data: function (){
            return {
                office_users: [],
                card_form: {
                    name: '',
                    assignee_id: 0,
                    team_ids: [],
                    due_date: '',
                    priority_status_id: 0,
                    progress_status_id: 0,
                    description: ''
                },
                card_tasks: [],
                task: {
                    name: ''
                },
                add_task: false,
                discussion_message: '',
                priority_status: [
                    {id: '1', name: 'High Priority'},
                    {id: '2', name: 'Medium Priority'},
                    {id: '3', name: 'Low Priority'},
                ],
                progress_status: [
                    {id: '1', name: 'Active'},
                    {id: '2', name: 'InActive'},],
                card: {},
                assign_user: false,
                task_assignee_id: 0,
                task_id: 0,
                add_deadline: false,
                deadline: "",
                add_subtask: false,
                subtask_name: "",
                view_subtasks: false,
                subtasks: {
                    id: 0,
                    name: "",
                    assignee: "",
                    due_date: "",
                    subtasks_count: 0,
                    subtasks: []
                },
                discussions: [],
                card_id: 0,
                task_update: {
                    id: 0,
                    name: '',
                    assignee_id: 0,
                    due_date: '',
                    card_id: 0,
                    status_id:0
                },
                task_name_update: false
            }
        },
        methods: {
            cardID(card_id){
                this.card_id = card_id;
            },
            getOfficeUsers(){
                axios.get('/task')
                    .then(response => {
                        this.office_users = response.data.office_users;
                    })
                    .catch(error => {
                        console.log(error.response);
                    });
            },
            getCardStatus(){
                axios.get('card/status')
                    .then(response => {
                        this.priority_status = response.data.priority_status;
                        this.progress_status = response.data.progress_status;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            inDays(due_date) {
                let t1 = new Date().getTime();
                let t2 = new Date(due_date).getTime();
                let days = parseInt((t2-t1)/(24*3600*1000));

                if(days < -500) return "N/A";
                if(days < -1 && days > -500) return days + "Days";
                if(days === 1) return "+"+days + " Day";
                if(days === -1) return days + " Day";
                if (days > 1) return "+"+days + " Days";

                return "N/A";
            },
            addTask(){
                this.add_task = !this.add_task;
            },
            saveTask(card_id){
                if (this.task.name.length === 0){
                    alert("Task name is required");
                    return false;
                }

                axios.post('/task', {
                    task: this.task,
                    card_id: card_id
                })
                    .then(response => {
                        this.card_tasks.push(response.data.task);
                        this.add_task = false;
                        this.task.name = "";
                        console.log(response.data);
                    })
                    .catch(function (error) {
                        console.log(error.response);
                    });
            },
            assignUser(task_id){
                this.task_id = task_id;
                this.assign_user = !this.assign_user;
            },
            saveAssignedUser(){
                if (this.task_assignee_id === 0){
                    alert("Please select a user")
                    return false;
                }
                axios.patch('/task/' + this.task_id, {
                    assignee_id: this.task_assignee_id
                })
                    .then(response => {
                        let res_task = response.data.task;
                        let ind = 0;

                        this.card_tasks.forEach(function (task, index) {
                            if (task.id === res_task.id){
                                ind = index;
                            }
                        });
                        this.card_tasks.splice(ind, 1, res_task);
                        this.assign_user = false;
                        this.task_assignee_id = 0;
                    })
                    .catch(function (error) {
                        console.log(error.response);
                    });
            },
            addDeadline(task){
                console.log(task)
                this.task_id = task.id;
                this.deadline = task.due_date;
                this.task_update.name = task.name;
                this.add_deadline = !this.add_deadline;
            },
            saveDeadline(){
                if (this.deadline.length === 0){
                    alert("A date is required")
                    return false;
                }

                axios.patch('/task/' + this.task_id, {
                    due_date: new Date(this.deadline).toISOString()
                })
                    .then(response => {
                        let res_task = response.data.task;
                        let ind = 0;

                        this.card_tasks.forEach(function (task, index) {
                            if (task.id === res_task.id){
                                ind = index;
                            }
                        });
                        this.card_tasks.splice(ind, 1, res_task);
                        this.add_deadline = false;
                        this.deadline = "";
                    })
                    .catch(function (error) {
                        console.log(error.response);
                    });
            },
            addSubtask(task_id){
                this.add_subtask = !this.add_subtask;
                this.task_id = task_id;
            },
            saveSubtask(){
                if (this.subtask_name.length === 0){
                    alert("subtask name is required")
                    return false;
                }

                axios.post('/task/subtask/'+this.task_id, {
                    name: this.subtask_name
                }).then(response => {
                    let ind;
                    this.card_tasks.forEach((task, index) => {
                        if (task.id === this.task_id){
                            ind = index
                        }
                    })

                    this.card_tasks[ind].sub_tasks.push(response.data.subtask);
                    this.card_tasks[ind].sub_tasks_count = this.card_tasks[ind].sub_tasks.length;
                    this.subtask_name = "";
                    console.log(response.data)
                }).catch(error => {
                    console.log(error.response)
                })
            },
            finishAddingSubtasks(){
                this.add_subtask = false;
            },
            viewSubtasks(task){
                this.subtasks.name = task.name;
                this.subtasks.assignee = task.assigned.first_name + " " + task.assigned.last_name;
                this.subtasks.due_date = task.due_date;
                this.subtasks.subtasks_count = task.sub_tasks.length;
                this.subtasks.subtasks = task.sub_tasks;
                this.view_subtasks = !this.view_subtasks;
            },
            saveDiscussionMessage(card_id){
                if (this.discussion_message.length === 0){
                    alert("discussion message is required");
                    return false;
                }

                axios.post('/discussion', {
                    card_id: card_id,
                    message: this.discussion_message
                }).then(response => {
                    this.discussion_message = "";
                    this.discussions.push(response.data.discussion);
                    console.log(this.discussions)
                }).catch(error => console.log(error.response));
            },
            updateCard(data){
                if (data.hasOwnProperty('name') && this.card_form.name.length === 0){
                    alert("Card name is required");
                    return false;
                }

                if (data.hasOwnProperty('assignee_id') && this.card_form.assignee_id < 1){
                    alert("select a valid user")
                    return false;
                }

                if (data.hasOwnProperty('team_ids') && this.card_form.team_ids.length === 0){
                    alert("Please select a valid team member is required")
                    return false;
                }

                if(data.hasOwnProperty('status_id') && this.card_form.progress_status_id < 1){
                    alert("select a valid status")
                    return false;
                }

                if (data.hasOwnProperty('priority_id') && this.card_form.priority_status_id < 1){
                    alert("Please select a valid priority status")
                    return false;
                }

                if (data.hasOwnProperty('description') && this.card_form.description.length === 0){
                    alert("Description is required")
                    return false;
                }

                axios.patch('/card/'+ data.card_id, data).then(response => {
                    if (data.hasOwnProperty('name')){
                        this.card_form.name = response.data.card.name;
                        this.$emit('update:name', response.data.card, "name")
                        alert("Card name updated successfully");
                    }

                    if (data.hasOwnProperty('assignee_id')){
                        this.card_form.assignee_id = response.data.card.assignee_id;
                        this.$emit('update:assignee', response.data.card, 'assignee')
                        alert("Assignee updated successfully")
                    }

                    if (data.hasOwnProperty('team_ids')){
                        this.card_form.team_ids = response.data.card.team_ids.split(', ');
                        alert("Team members updated successfully")
                    }

                    if(data.hasOwnProperty('status_id')){
                        this.card_form.progress_status_id = response.data.card.status_id;
                        this.$emit('update:status', response.data.card, "status")
                        alert("Status updated successfully")
                    }

                    if (data.hasOwnProperty('priority_id')){
                        this.card_form.priority_status_id = response.data.card.priority_id;
                        this.$emit('update:priority', response.data.card, "priority")
                        alert("Priority status updated successfully")
                    }

                    if (data.hasOwnProperty('description')){
                        this.card_form.description = response.data.card.description;
                        alert("Description updated successfully")
                    }
                }).catch(err => console.log(err))
            },
            updateCardDuedate(date){
                if (this.card_form.due_date.length === 0 || this.card_id === 0){
                    alert("please select valid date");
                    return false;
                }

                axios.patch('/card/'+ this.card_id, {
                    due_date: new Date(date).toISOString(),
                }).then(response =>{
                    this.card_form.due_date = response.data.card.due_date;
                    this.$emit("update:duedate", response.data.card, "duedate");
                    console.log(this.card_form.due_date)
                    alert("Due Date was updated successfully")
                }).catch(err => console.log(err.response))
            },
            initUpdateTask(task){
                this.task_update.id = task.id
                this.task_update.name = task.name;
                this.task_update.assignee_id = task.assignee_id;
                this.task_update.due_date = task.due_date;
                this.task_update.card_id = task.card_id;
                this.task_update.status_id = task.status_id;
                this.task_name_update = true;
            },
            updateTask(data){
                if (data.hasOwnProperty('name') && data.name.length === 0){
                    alert("Task name is required");
                    return false;
                }

                console.log(data)

                axios.patch('/task/'+data.task_id, data).then(response =>{
                    console.log(response.data)
                    this.task_update.name = response.data.task.name;
                    this.task_name_update = false;
                    if (response.data.task.parent_id === null){
                        this.card_tasks.forEach((task, index) =>{
                            if (task.id == data.task_id){
                                task.name = response.data.task.name;
                            }
                        })
                    }else{
                        this.subtasks.subtasks.forEach((subtask, index) => {
                            if (subtask.id == data.task_id){
                                subtask.name = response.data.task.name;
                            }
                        })
                    }
                }).catch(err => console.log(err.response))
            }
        }
    }
</script>
<style scoped>
    .view-card{
        position: absolute;
        right: 0;
        top: 8.188rem;
        z-index: 10;
        background: #FFFFFF;
        overflow-y: auto!important;
    }
    .add-task{
        border: none;
        border-left: 3px solid #0b2354;
    }
</style>