<template>
  <div class="view-card elevation-4" style="width: 800px;">
    <div class="card-header">
      <small style="color: #06496f;opacity: 0.5;">{{cardDetails.section_name}}</small>
      <div class="row">
        <div class="col-md-7 m-0 pt-0 pb-0 pr-0">
          <h5 v-on:click="renameCard(card_form.name)" v-if="card_form.updateFlag === 0" style="margin-top: 10px;">{{card_form.name}}</h5>
          <input v-if="card_form.updateFlag === 1" type="text" v-bind:class="{'is-invalid' : hasError}"
                 v-model="card_form.name"
                 v-on:keyup.enter="updateCard({card_id: cardDetails.id, name: card_form.name})">
        </div>
        <div class="col-md-5 m-0 text-right">
          <!--<a href="javascript:void(0)"  @click="attachDocument()" class="badge badge-success"><i class="fas fa-paperclip"></i> Attach Document</a>-->
          <a href="javascript:void(0)" v-if="card_form.document === '' || card_form.document === null" v-on:click="attachDocument" style="color: #ffffff;" class="badge badge-success"><i class="fas fa-paperclip"></i> Attach Document</a>
          <span v-if="card_form.document !== '' && card_form.document !== null" style="color: #ffffff;" class="badge badge-default"><a :href="'/storage/pipeline/documents/' + card_form.document" style="color:#FFFFFF;" download><i class="fas fa-file"></i> {{card_form.document}} </a><a href="javascript:void(0)" class="text-danger" @click="deleteDocument(cardDetails.id)"><i class="fas fa-trash"></i></a></span>
          <input style="display: none;" type="file" :id="'file-card'" @change="attachedDocument(cardDetails.id)" class="form-control form-control-sm col-md-12" value="Add File"/>
          <a href="javascript:void(0)"  @click="sendEmailTemplate()" class="badge badge-primary"><i class="far fa-envelope"></i> Send Email</a>
        </div>
      </div>
    </div>
    <div class="card-body" style="padding:0 0.7rem 0.7rem 0.7rem;direction:rtl;">
        <div style="direction: ltr">
        <div class="row" style="margin-top:0px;direction: ltr;">
          <div class="col-md-12 pb-0 pt-0">
            <small>Case Summary</small>
            <input type="text" v-model="card_form.summary_description" class="form-control form-control-sm">
          </div>
        </div>
        <div class="row" style="margin-top:1rem;">
          <div class="col-md-12 mb-0 mt-0 pt-0">
            <small class="card-lables">Client</small>
            <h5 v-on:click="updateClient(card_form.client_name)" v-if="card_form.updateClient === 0 && card_form.client_name !== ''" style="margin-top: 0px;margin-bottom: 0px;">{{card_form.client_name}}</h5>
            <h5 v-on:click="updateClient(card_form.client_name)" v-if="card_form.updateClient === 0 && card_form.client_name === null" style="margin-top: 0px;margin-bottom: 0px;">None</h5>
            <multiselect  v-bind:class="{'is-invalid' : hasError}" v-if="card_form.updateClient === 1" @input="updateClient(card_form.client_name)" v-model="card_form.client_name" :custom-label="customLabel" placeholder="Select one" :options="office_clients" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
            </multiselect>
          </div>
        </div>
        <!--<div class="row" style="margin-top:1rem;">
          <div class="col-md-4 mb-0 mt-0 pt-0">
            <small>Insurer</small>
            <input type="text" class="form-control form-control-sm" v-model="card_form.insurer" />
          </div>
          <div class="col-md-4 mb-0 mt-0 pt-0">
            <small class="card-lables">Policy</small>
            <input type="text" class="form-control form-control-sm" v-model="card_form.policy" />
          </div>
          <div class="col-md-4 mb-0 mt-0 pt-0">
            <small class="card-lables">Dependency</small>
            <select class="form-control form-control-sm" v-model="card_form.dependency_id">
              <option value="" disabled>Select Card</option>
              <option v-for="card in cards_drop_down" :value="card.id">{{card.name}}</option>
            </select>
          </div>
        </div>-->
        <div class="row" style="margin-top:0px;">
          <div class="col-md-4 mb-0 mt-0 pt-0">
            <small class="card-lables">Sales representative on record</small>
            <select class="form-control form-control-sm" v-model="card_form.assignee_name"  v-bind:class="{'is-invalid' : hasError}">
              <option value="" disabled>Sales representative user on record</option>
              <option v-for="user in office_advisors" :value="user">{{user}}</option>
            </select>
          </div>
          <div class="col-md-8 mb-0 mt-0 pt-0">
            <small class="card-lables">Assign Team members</small>
            <multiselect v-model="card_form.team_names" :custom-label="customLabel" placeholder="Select one" :options="office_users" :searchable="false" :allow-empty="true" :multiple="true" :close-on-select="true">
            </multiselect>
          </div>
        </div>
        <div class="row" style="margin-top:0px;">
          <div class="col-md-6 mb-0 mt-0 pt-0">
            <small class="card-lables">Upfront Revenue</small>
            <input type="number" class="form-control form-control-sm" v-model="card_form.upfront_revenue" />
          </div>
          <div class="col-md-6 mb-0 mt-0 pt-0">
            <small class="card-lables">Ongoing Revenue</small>
            <input type="number" class="form-control form-control-sm" v-model="card_form.ongoing_revenue" />
          </div>
        </div>
        <div class="row" style="margin-top:0px;">
          <div class="col-md-4 pb-0 pt-0">
            <small class="card-lables">Due Date</small>
            <DueDate  :open.sync="card_form.open"
                @pick="handleChange2()"
                v-model="card_form.due_date"
                type="format"
                :placeholder="card_form.due_date"
                v-bind:class="{'is-invalid' : hasError}"
            ></DueDate>
          </div>
          <div class="col-md-4 pb-0 pt-0">
            <small class="card-lables">Status</small>
            <select v-model="card_form.progress_status_id" class="form-control form-control-sm"  v-bind:class="{'is-invalid' : hasError}">
              <option disabled>Select Priority</option>
              <option v-for="status in progress_status" :value="status.id">{{status.name}}</option>
            </select>
          </div>
          <div class="col-md-4 pb-0 pt-0">
            <small class="card-lables">Priority</small>
            <select v-model="card_form.priority_status_id" class="form-control form-control-sm"  v-bind:class="{'is-invalid' : hasError}">
              <option disabled>Select Priority</option>
              <option v-for="status in priority_status" :value="status.id" v-bind:style="{color: status.fcolor}"><span>{{status.name}}</span></option>
            </select>
          </div>
        </div>
        <div class="row" style="margin-top:1rem;border-bottom: 2px solid #b5c9d4;margin-bottom:1rem;direction: ltr;">
          <div class="col-md-12 pb-0 pt-0">
            <small class="card-lables">Description</small>
            <textarea v-model="card_form.description" rows="7" class="form-control form-control-sm">{{card_form.description}}</textarea>
          </div>
        </div>
        <!--<p class="card-task-heading"><strong>Tasks</strong><a href="javascript:void(0)" class="float-right" @click="addTask"><i class="fa fa-plus"></i> Add task</a></p>-->
        <p><strong>Tasks</strong><a href="javascript:void(0)" class="text-primary float-right" @click="addTask"><i class="fa fa-plus"></i> Add task</a></p>
        <div class="col template-tasks">
          <div style="position:relative;border-bottom:1px solid #ecf1f4;padding-bottom:0.5rem;margin-bottom:0.5rem;" v-for="(taskItems, task_item_index) in card_tasks" :class="'task_'+task_item_index">
            <div class="row">
              <div class="col-lg-10 pt-0 pb-0">
                <input type="hidden" class="form-control form-control-sm task-name" v-model="taskItems.id">
                <input v-bind:class="{'is-invalid' : hasError}" v-if="taskItems.editTask" type="text" class="form-control form-control-sm task-name" placeholder="Add task name" v-model="taskItems.name">
                <span v-if="!taskItems.editTask" @click="enableTask(task_item_index)" style="line-height:3rem;">{{taskItems.name}}</span>
              </div>
              <div class="col-lg-2 text-right pt-0 pb-0">
                <a href="#" class="mr-1" v-show="taskItems.status" @click="uncompleteTask(task_item_index)"><i class="fas fa-check text-success mr-1"></i></a>
                <a href="#" class="mr-1" v-show="!taskItems.status" @click="completeTask(task_item_index)"><i class="fas fa-check text-default"></i></a>
                <a href="#" @click="deleteTask(task_item_index)"><i class="fas fa-trash"></i></a>
              </div>
            </div>
            <span class="float-left">
            <small @click="assignTask(task_item_index)" v-if="taskItems.selected_assignee === ''"><i class="fas fa-user"></i> + Assign to a person</small>
            <small @click="assignTask(task_item_index)" v-if="taskItems.selected_assignee !== '' || taskItems.assignee_name !== ''"><i class="fas fa-user"></i>  {{taskItems.selected_assignee !== '' && taskItems.selected_assignee !== null ? taskItems.selected_assignee : taskItems.assignee_name}}</small>
            <!--<small @click="assignTask(task_item_index)" v-if="taskItems.assignee_name !== null"><i class="fas fa-user"></i>  {{taskItems.assignee_name}}</small>-->
            <div v-if="taskItems.assign_user" class="assign-person elevation-3">
              <div class="row pr-0 pb-0 pt-0 m-0">
                <div class="col-md-9 pl-0 pt-0 pb-0">
                <select v-model="taskItems.assignee" class="form-control form-control-sm" @change="taskUserAssigned(task_item_index)">
                  <option disabled>Select User</option>
                  <option v-for="user in office_users" :value="user">{{user}}</option>
                </select>
                </div>
              </div>
          </div>
          </span>
            <span class="float-right">
              <small @click="addDeadline(task_item_index)" v-if="taskItems.selected_duedate === '' || taskItems.due_date === ''"><span class="text-danger"><i class="fas fa-calendar-alt"></i></span> + Add deadline</small>
              <small @click="addDeadline(task_item_index)" v-if="taskItems.selected_duedate !== '' && taskItems.due_date !== ''"><span class="text-danger"><i class="fas fa-calendar-alt"></i></span> {{taskItems.selected_duedate !== '' && taskItems.selected_duedate !== null ? taskItems.selected_duedate : taskItems.due_date}}</small>
              <div class="assign-deadline elevation-3" v-show="taskItems.add_deadline">
                <DueDate v-model="taskItems.due_date" :open.sync="taskItems.open6" @pick="handleChange(task_item_index)" type="date" :use-utc="false"></DueDate>
              </div>
              <small @click="addSubTasks(task_item_index)" class="ml-1">
                <span v-if="taskItems.sub_tasks && taskItems.sub_tasks.length === 0"><span class="text-info"><i class="fas fa-network-wired"></i></span> + Add subtasks</span>
                <span v-if="taskItems.sub_tasks && taskItems.sub_tasks.length > 0"><span class="text-info"><i class="fas fa-network-wired"></i></span> {{taskItems.sub_tasks.length}} {{ taskItems.sub_tasks.length !== 1 ? "subtasks" : "subtask" }}</span>
              </small>
            </span>
            <div class="clearfix"></div>

          <div style="position:relative;display: block;" v-if="taskItems.add_sub_task">
            <div class="row subtask" v-for="(subtaskItems, subtask_item_index) in taskItems.sub_tasks">

              <div class="col-md-10 pt-0 pb-0">
                <input type="hidden" class="form-control form-control-sm task-name" v-model="subtaskItems.id" :class="'subtask_'+subtask_item_index">
                <input type="text" v-bind:class="{'is-invalid' : hasError}" v-show="subtaskItems.editSubtask" class="form-control form-control-sm task-name" placeholder="Add subtask name" v-model="subtaskItems.name">
                <span v-if="!subtaskItems.editSubtask" @click="enableSubtask(task_item_index,subtask_item_index)" style="line-height:3rem;">{{subtaskItems.name}}</span>
              </div>
              <div class="col-md-2 text-right pt-0 pb-0">
                <a href="#" class="mr-1" v-show="subtaskItems.status && !subtaskItems.editSubtask" @click="uncompleteSubtask(task_item_index,subtask_item_index)"><i class="fas fa-check text-success"></i></a>
                <a href="#" class="mr-1" v-show="!subtaskItems.status && !subtaskItems.editSubtask" @click="completeSubtask(task_item_index,subtask_item_index)"><i class="fas fa-check text-default"></i></a>
                <a href="#" class="text-danger mr-1" @click="addSubtaskDeadline(task_item_index,subtask_item_index)"><i class="far fa-calendar-alt"></i></a>
                <div class="assign-deadline2 elevation-3" v-show="subtaskItems.add_deadline">
                  <DueDate v-model="subtaskItems.due_date" autocomplete="off" :open.sync="subtaskItems.open4" @pick="handleChange4(task_item_index,subtask_item_index)" type="timestamp"></DueDate>
                </div>
                <a href="#" @click="deleteSubtask(task_item_index,subtask_item_index)" class="delete-subtask"><i class="fas fa-trash"></i></a>
              </div>

            </div>
            <div class="addsubtask">
              <a href="javascript:void(0)" @click="addSubTask(task_item_index)">+ Add Subtask</a>
            </div>
          </div>

          </div>
        </div>
      <div class="card-discussion" style="direction: ltr">
        <p><strong>Discussion</strong></p>
        <div class="row">
          <div class="col-md-10 pt-0 pb-0 pr-0 m-0">
            <input type="text" v-model="discussion_message" class="form-control form-control-sm">
          </div>
          <button type="button" @click="saveDiscussionMessage(cardDetails.id)" class="btn btn-sm btn-primary float-right ml-1">Post</button>
        </div>
        <div v-for="discussion in discussions">
          <strong>{{ (discussion.user.first_name?discussion.user.first_name:"") + " " + (discussion.user.last_name?discussion.user.last_name:"")}}</strong>
          <p>{{discussion.message}}</p>
          <hr>
        </div>
      </div>
        <div class="card-recordings">
        <p><strong>Voice Notes</strong></p>
        <div>
          <vue-record-audio @result="onResult" style="height:40px !important;width:40px !important;" />
          <audio id="audioPlayer" controls src="#" type="audio/mpeg" style="height:40px !important;width:calc(100% - 140px);"></audio>
          <a href="#" type="button" class="btn btn-secondary" style="margin-left: 7px;position:relative;top:-16px;line-height:1.25rem;margin-bottom:0px;" @click="saveVoice(cardDetails.id)" >Save</a>
        </div>
        <hr>
        <div v-for="voicenote in recordings">
          <div>
            <audio class="audioPlayer" controls :src="'/storage/recording/' + voicenote.recording" type="audio/mpeg" style="width:100%;height:40px;"></audio>
            <small class="text-muted float-left" style="margin-left: 10px;position:relative;font-size:65%;padding-bottom:7px;"><i class="fas fa-user"></i> {{ voicenote.user }}</small>
            <small class="text-muted float-right" style="margin-left: 10px;position:relative;font-size:65%;padding-bottom:7px;"><i class="fas fa-clock"></i> {{ voicenote.created_at }}</small>
          </div>
          <hr>
        </div>
      </div>
        </div>
    </div>
    <div class="card-footer clearfix">
      <button class="btn btn-sm btn-outline-info" @click="closeCard">Cancel</button>
      <button type="button" @click="saveTask(cardDetails)"  class="btn btn-sm btn-success float-right">Save Case</button>
    </div>


    <div class="modal-mask" v-show="link_client" transition="modal" aria-hidden="true" role="dialog" style="position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);">
      <div class="modal-dialog">
        <div class="modal-content" style="width:800px;">
          <div class="modal-header">

            <span class="modal-title">Link Client</span>
            <button type="button" class="close" @click="closeLinkClient()">&times;</button>
          </div>
          <div class="modal-body link-client-modal" style="overflow:inherit;">
            <multiselect v-model="card_form.client_name" :custom-label="customLabel" placeholder="Select one" :options="office_clients" :searchable="true" :close-on-select="true">
            </multiselect>
            <button class="btn btn-sm btn-default float-right ml-1" @click="closeLinkClient()">Cancel</button>
            <button class="btn btn-sm btn-primary float-right" @click="updateCard({card_id: cardDetails.id, client_name: card_form.client_name})">Save</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-mask" v-show="email_template" transition="modal" aria-hidden="true" role="dialog" style="position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);">
      <div class="modal-dialog">
        <div class="modal-content" style="width:800px;">
          <div class="modal-header">

            <span class="modal-title">Choose Email Template</span>
            <button type="button" class="close" @click="closeEmailTemplate()">&times;</button>
          </div>
          <div class="modal-body link-client-modal" style="overflow:inherit;">
            <select class="form-control form-control-sm col-md-12 pt-0 pb-0" v-model="emailTemplate" title="Position">
              <option value="0" disabled hidden>Select Email Template</option>
              <option value="0">No Template</option>
              <option v-for="(value,key) in emailTemplates" :value="key">{{value}}</option>
            </select><br /><br />
            <button class="btn btn-sm btn-default float-right ml-1" @click="closeEmailTemplate()">Cancel</button>
            <button class="btn btn-sm btn-primary float-right" @click="saveEmailTemplate()">Ok</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-mask" v-show="send_email" transition="modal" aria-hidden="true" role="dialog" style="position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);">
      <div class="modal-dialog">
        <div class="modal-content" style="width:800px;">
          <div class="modal-header">

            <span class="modal-title">Send Email</span>
            <button type="button" class="close" @click="closeSendEmail()">&times;</button>
          </div>
          <div class="modal-body link-client-modal" style="overflow:inherit;">
            <input type="text" class="form-control form-control-sm task-name" placeholder="Add email addresses" v-model="emailAddresses">
            <input type="text" class="form-control form-control-sm task-name" placeholder="Add email subject" v-model="emailSubject">
            <tinymce-editor api-key="361nrfmxzoobhsuqvaj3hyc2zmknskzl4ysnhn78pjosbik2" v-model="emailContent" :init="{content_style: 'body { font-family: Arial; }',inline_styles:'',height:'200px',width:'100%',menubar:'',plugins: 'wordcount advlist lists table',branding:false,toolbar:'undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'}" ></tinymce-editor><br /><br />
            <button class="btn btn-sm btn-default float-right ml-1" @click="closeSendEmail()">Cancel</button>
            <button class="btn btn-sm btn-primary float-right" @click="saveSendEmail()">Send</button>
          </div>
        </div>
      </div>
    </div>

    <!--<div class="modal-mask" v-show="attach_document" transition="modal" aria-hidden="true" role="dialog" style="position: fixed;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);">
      <div class="modal-dialog">
        <div class="modal-content" style="width:500px;">
          <div class="modal-header">

            <span class="modal-title">Attachments</span>
            <button type="button" class="close" @click="attachDocument()">&times;</button>
          </div>
          <div class="modal-body link-client-modal" style="overflow:inherit;">
            <div class="attachedDocumentList">
                <span v-for="attachedDocumentlst in attachedDocuments">{{ attachedDocumentlst.filename}}</span>
            </div>
            <v-file-input
                    v-model="card_form.files"
                    small-chips
                    clearable
                    accept=".*"
                    loading="true"
                    prepend-icon="mdi-paperclip"
                    clear-icon="mdi-delete"
                    @change="attachedDocument(cardDetails.id)"
            >
            </v-file-input>
          </div>
          <div class="modal-footer">
            <button class="btn btn-sm btn-default float-right ml-1" @click="attachDocument()">Cancel</button>
            <button class="btn btn-sm btn-primary float-right" @click="saveSendEmail()">Send</button>
          </div>
        </div>
      </div>
    </div>-->
  </div>
</template>

<script>
  import Multiselect from 'vue-multiselect'
  import DatePicker from 'vue2-datepicker';
  import 'vue2-datepicker/index.css';
  import VueRecord from '@codekraft-studio/vue-record';
  import AudioRecorder from 'vue-audio-recorder'


export default {
  name: "Card.vue",
  props: ["cardDetails"],
  components:{
    "DueDate": DatePicker,
    "multiselect": Multiselect,
    'tinymce-editor': Editor, // <- Important part
    'VueRecord' : VueRecord,
    'AudioRecorder' : AudioRecorder,

  },
  mounted(){
    this.getCardsDropDown();
    this.getOfficeUsers();
    this.getOfficeClients();
    this.getOfficeAdvisors();
    this.getCardStatus();
    this.getEmailTemplates();
  },
  watch: {
    'cardDetails': function (val, oldVal) {
      this.card_form.client_name = val.client_name;
      this.card_form.insurer = val.insurer;
      this.card_form.policy = val.policy;
      this.card_form.upfront_revenue = val.upfront_revenue;
      this.card_form.ongoing_revenue = val.ongoing_revenue;
      this.card_form.dependency_id = val.dependency_id;
      this.card_form.name = val.name;
      this.card_form.document = val.document;
      this.card_form.assignee_id = val.assignee_id;
      this.card_form.assignee_name = val.assignee_name;
      this.card_form.team_ids = val.team_ids ? val.team_ids.split(", ") : [];
      this.card_form.team_names = val.team_names ? val.team_names.split(", ") : [];
      this.card_form.due_date = val.due_date;
      this.card_form.priority_status_id = val.priority_id;
      this.card_form.priority_status = [];
      this.card_form.office_advisors = [];
      this.card_form.progress_status_id = val.status_id;
      this.card_form.summary_description = val.summary_description;
      this.card_form.description = val.description;
      this.card_tasks = val.tasks;
      this.discussions = val.discussions;
      this.attachedDocuments = [];
      this.card_tasks.sub_tasks = [];
      this.recordings = val.recordings;

    },
    internalValue(v){
      this.$emit('input', v);
    }
  },
  data: function (){
    return {
      hasError:false,
      emailTemplate:0,
      emailTemplates:[],
      emailContent:'',
      emailAddresses:'',
      emailSubject:'',
      email_template: false,
      attach_document: false,
      selectedObjects: [],
      cards_drop_down: [],
      office_users: [],
      office_advisors: [],
      office_clients: [],
      card_form: {
        insurer: '',
        policy: '',
        upfront_revenue: '',
        ongoing_revenue: '',
        dependency_id: '',
        name: '',
        assignee_id: 0,
        assignee_name: '',
        team_ids: [],
        team_names: [],
        due_date: '',
        priority_status_id: 0,
        progress_status_id: 0,
        description: '',
        updateFlag: 0,
        updateClient: 0,
        files: '',
        document: '',
      },
      card_tasks: [],
      task: {
        name: '',
        subTasks:[],
        add_sub_task: false,
      },
      add_task: false,
      add_sub_task: false,
      priority_status: [
        {id: '1', name: 'High Priority', fcolor: '#FF0000'},
        {id: '2', name: 'Medium Priority', fcolor: '#00FF00'},
        {id: '3', name: 'Low Priority', fcolor: '#0000FF'},
      ],
      progress_status: [
        {id: '1', name: 'Active'},
        {id: '2', name: 'InActive'},],
      card: {},
      task_assignee_id: 0,
      task_id: 0,
      add_deadline: false,
      deadline: "",
      link_client: false,
      send_email: false,
      client_name: '',
      add_subtask: false,
      subtask_name: "",
      view_subtasks: false,
      subTasks: {
        id: 0,
        name: "",
        assignee: "",
        due_date: "",
        subtasks_count: 0,
        subtasks: [],
        add_sub_task: false,
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
      task_name_update: false,
      internalValue: this.value,
      attachedDocuments:[],
      discussion_message: '',
      recordedAudio: null,
      recordings: [],
    }
  },
  methods: {
    deleteDocument(card_id){
      let self = this;
      let formData = new FormData();

      /*console.log(document.getElementById("file-card").files[0]);*/
      formData.append('documentFile', document.getElementById("file-card").files[0]);
      formData.append('card_id', card_id);

      axios.post('/card/deletedocument', formData,
              {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              })
              .then(function (response) {

                if(!response.data){
                  alert('File not deleted.');
                }else{
                  alert('File successfully deleted.');
                  self.card_form.document = '';
                }

              })
              .catch(function (error) {
                /*console.log(error);*/
              });
    },
    attachedDocument(card_id){
      let self = this;
      let formData = new FormData();

      /*console.log(document.getElementById("file-card").files[0]);*/
      formData.append('documentFile', document.getElementById("file-card").files[0]);
      formData.append('card_id', card_id);

      axios.post('/card/attachdocument', formData,
              {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              })
              .then(function (response) {

                if(!response.data){
                  alert('File not uploaded.');
                }else{
                  alert('File successfully uploaded.');
                  self.card_form.document = response.data.filename;
                }

              })
              .catch(function (error) {
                /*console.log(error);*/
              });
    },
    attachDocument(){
      document.getElementById("file-card").click();
      this.attach_document = !this.attach_document;
    },
    closeCard(){
      this.$emit('close-me')
    },
    sendEmailTemplate(){
      this.email_template = true;
    },
    closeEmailTemplate(){
      this.emailSubject = '';
      this.emailContent = '';
      this.email_template = false;
    },
    saveEmailTemplate(){
      axios.get('/email_template/' + this.emailTemplate)
              .then(response => {
                this.email_template = false;
                this.send_email = true;
                this.emailSubject = response.data.email_subject;
                this.emailContent = response.data.email_content;
                this.emailTemplates.$forceUpdate();

              })
              .catch(error => {
                this.email_template = false;
                /*console.log(error.response);*/
              });
    },
    closeSendEmail(){
      this.emailSubject = '';
      this.emailContent = '';
      this.send_email = false;
    },
    saveSendEmail(){
      axios.post('/send_email_template',{addresses: this.emailAddresses, subject: this.emailSubject, emailbody: this.emailContent})
              .then(response => {
                this.emailTemplate = '';
                this.emailSubject = '';
                this.emailContent = '';
                this.send_email = false;
                this.email_template = false;
                this.$emit('close-me');
              })
              .catch(error => {
                this.send_email = false;
                this.email_template = false;
                /*console.log(error.response);*/
              });
    },
    saveLinkClient(){
      this.link_client = false;
    },
    closeLinkClient(){
      this.closedLinkClient = true;
      this.link_client = false;
    },
    linkClient(){
      this.link_client = true;
    },
    taskUserAssigned(index){
      this.card_tasks[index].selected_assignee = this.card_tasks[index].assignee;
      this.card_tasks[index].assignee_name = this.card_tasks[index].assignee;
      this.card_tasks[index].assign_user = !this.card_tasks[index].assign_user;
    },
    assignTask(index){
      this.card_tasks[index].assignee = this.card_tasks[index].assignee_name;
      this.office_users = [];
      axios.get('/task')
              .then(response => {
                this.office_users = response.data.office_users;
                this.office_users.$forceUpdate();

              })
              .catch(error => {
                /*console.log(error.response);*/
              });

      this.card_tasks[index].add_deadline = false;
      this.card_tasks[index].add_sub_tasks = false;
      this.card_tasks[index].assign_user = !this.card_tasks[index].assign_user;
      //console.log(this.card_tasks[index].assign_user);
    },
    handleChange2() {
      const d = new Date(this.card_form.due_date);
      const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

      this.card_form.due_date = t;
      this.card_form.open = !this.card_form.open;
    },
    handleChange(index) {
      const d = new Date(this.card_tasks[index].due_date);
        const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);

      //console.log(t);

        this.card_tasks[index].assign_task = false;
        this.card_tasks[index].add_sub_task = false;
        this.card_tasks[index].selected_duedate = t;
        this.card_tasks[index].add_deadline = !this.card_tasks[index].add_deadline;

        this.card_tasks[index].open6 = false;

    },
    handleChange4(index,subindex) {
      const d = new Date(this.card_tasks[index].sub_tasks[subindex].due_date);
      const t = d.getFullYear()  + "-" + (d.getMonth() + 1) + "-" + (d.getDate() + 0);



      this.card_tasks[index].sub_tasks[subindex].add_deadline = false;
      this.card_tasks[index].sub_tasks[subindex].open4 = false;

    },
    customLabel (option) {
      return `${option}`
    },
    cardID(card_id){
      this.card_id = card_id;
    },
    getOfficeClients(){
      axios.get('/card/get-office-clients')
              .then(response => {
                this.office_clients = response.data.office_clients;
                //console.log(response.data.office_clients);
              })
              .catch(error => {
                /*console.log(error.response);*/
              });
    },
    getOfficeUsers(){
      axios.get('/task/advisor')
              .then(response => {
                this.office_users = response.data.office_users;
                this.office_users.$forceUpdate();

              })
              .catch(error => {
                /*console.log(error.response);*/
              });
    },
    getOfficeAdvisors(){
      axios.get('/task/advisor')
              .then(response => {
                this.office_advisors = response.data.office_users;
                this.office_advisors.$forceUpdate();

              })
              .catch(error => {
                /*console.log(error.response);*/
              });
    },
    getCardsDropDown(){
      axios.get('/card/get-cards')
      .then(response => {
        /*console.log('card_drop_down', response.data.cards);*/
        this.cards_drop_down = response.data.cards;
        this.cards_drop_down.$forceUpdate();
      })
      .catch(error => {
        /*console.log(error.response);*/
      });
    },
    getEmailTemplates(){
      axios.get('/email_templates')
              .then(response => {
                this.emailTemplates = response.data;
                this.emailTemplates.$forceUpdate();

              })
              .catch(error => {
                /*console.log(error.response);*/
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
    renameCard(cardName){
      this.card_form.updateFlag = 1;
      this.card_form.name = cardName;
      this.addSectionFlag = false;
    },
    updateClient(clientName){
      if(this.card_form.updateClient === 0){
        this.card_form.updateClient = 1;
      } else {
        this.card_form.updateClient = 0;
      }
      this.card_form.client_name = clientName;
      this.addSectionFlag = false;
    },
    addTask() {
      let i = (this.card_tasks.length);

      if (!this.card_tasks) {
        //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
        //[];
      }


      this.card_tasks.push(
              {
                id: null,
                name: "",
                assignee_id: null,
                assignee_name: '',
                sub_tasks: [],
                subTasks: [],
                date: null,
                add_deadline: false,
                assign_user: false,
                open:false,
                add_sub_task:false,
                selected_assignee: '',
                assignee: '',
                selected_duedate: '',
                editTask: true
              }
      );
      /*this.add_task = true;*/
    },
    addSubTasks(index){
      this.card_tasks[index].assign_task = false;
      this.card_tasks[index].add_deadline = false;
      if(this.card_tasks[index].add_sub_task){
        this.card_tasks[index].add_sub_task = false;
        this.$forceUpdate();
      } else {
        this.card_tasks[index].add_sub_task = true;
        this.$forceUpdate();
      }

      let i = (this.card_tasks.length);

      if (!this.card_tasks) {
        //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
        //[];
      }

      if(this.card_tasks[index].sub_tasks.length === 0) {
        this.card_tasks[index].sub_tasks.push({
          name: "",
          open4: false,
          selected_duedate: '',
          add_deadline: false,
          editSubtask: true
        });
      }
    },
    addSubTask(index){
      let i = (this.card_tasks.length);

      if (!this.card_tasks) {
        //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
        //[];
      }

      this.card_tasks[index].sub_tasks.push({
        name:"",
        open4:false,
        selected_duedate:'',
        add_deadline: false,
        editSubtask: true
      });
    },
    assignUser(index){
      if(this.card_tasks[index].assign_user){
        this.card_tasks[index].assign_user = false;
      } else {
        this.card_tasks[index].assign_user = true;
      }
    },
    addDeadline(index){
      if(this.card_tasks[index].add_deadline) {
        this.card_tasks[index].add_deadline = false;
        this.$forceUpdate();
      } else {
        this.card_tasks[index].add_deadline = true;
        this.$forceUpdate();
      };
      /*console.log(this.card_tasks[index].add_deadline);*/
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
        toastr.warning('<strong>Warning!</strong> Discussion message is required.');

        toastr.options.timeOut = 1000;
        return false;
      }

      axios.post('/discussion', {
        card_id: card_id,
        message: this.discussion_message
      }).then(response => {
        this.discussion_message = "";
        this.discussions.push(response.data.discussion);
        toastr.success('<strong>Success!</strong> Discussion successfully saved.');

        toastr.options.timeOut = 1000;
      }).catch(error => console.log(error.response));
    },
    saveTask(section){
      let err = 0;

      if (this.card_form.name.length === 0){
        err++;
      }

      if (this.card_form.assignee_name.length === 0){
        err++;
      }

      if (this.card_form.due_date.length === 0){
        err++;
      }

      if (this.card_form.progress_status_id.length === 0){
        err++;
      }

      if (this.card_form.priority_status_id.length === 0){
        err++;
      }


      for (var i = 0; i < this.card_tasks.length; i++) {
        if($('.task_'+i).is(':visible')) {
          if (this.card_tasks[i].name === "Add task name" || this.card_tasks[i].name === '') {
            err++;
          }

          for (var j = 0; j < this.card_tasks[i].sub_tasks.length; j++) {
            if (this.card_tasks[i].sub_tasks[j].name === "Add subtask name" || this.card_tasks[i].sub_tasks[j].name === "") {
              err++;
            }

            if (this.card_tasks[i].sub_tasks[j].name === null) {
              err++;
            }
          }

          this.card_tasks[i]["section_id"] = section.section_id;
        }
      }

      if(err > 0){
        this.hasError = true;
        return false;
      } else {
        this.hasError = false;
      }

      axios.post('/card', {
        section: section,
        task: this.card_tasks,
        card_form: this.card_form,
        card_name: this.card_form.name,
        client_name: this.card_form.client_name
      })
              .then(response => {
                console.log(response.data.Card);
                this.card_tasks = response.data.Card.tasks;
                /*this.$emit('new-task', response.data.Card);*/
                this.$emit('update:name', response.data.Card, "name");
                this.$emit('update:summary_description', response.data.Card, "summary_description");
                this.$emit('update:description', response.data.Card, "description");
                this.$emit('update:insurer', response.data.Card, "insurer");
                this.$emit('update:policy', response.data.Card, "policy");
                this.$emit('update:upfront_revenue', response.data.Card, "upfront_revenue");
                this.$emit('update:ongoing_revenue', response.data.Card, "ongoing_revenue");
                this.$emit('update:dependency_id', response.data.Card, "dependency_id");
                this.$emit('update:assignee', response.data.Card, 'assignee');
                this.$emit('update:client_name', response.data.Card, 'client_name');
                this.$emit('update:status', response.data.Card, "status");
                this.$emit('update:priority', response.data.Card, "priority");
                this.$emit('update:due_date', response.data.Card, "due_date");
                this.$emit('update:tasks', response.data.Card, "tasks");
                this.card_tasks.forEach((task, ind) => {
                  task.open = false;
                  task.add_sub_task = false;
                });
                toastr.success('<strong>Success!</strong> Card was successfully saved.');

                toastr.options.timeOut = 1000;
                this.$emit('close-me')

              })
              .catch(function (error) {
                toastr.error('<strong>Error!</strong> An unexpected error occured when trying to save the card.');

                toastr.options.timeOut = 1000;
              });
    },
    addSubtaskDeadline(index,subindex){
      this.card_tasks[index].sub_tasks[subindex].add_deadline = !this.card_tasks[index].sub_tasks[subindex].add_deadline;
    },
    enableTask(index){
      this.card_tasks[index].editTask = !this.card_tasks[index].editTask;
    },
    enableSubtask(index,subindex){
      this.card_tasks[index].sub_tasks[subindex].editSubtask = !this.card_tasks[index].sub_tasks[subindex].editSubtask;
    },
    uncompleteTask(index){
      axios.post('/task/update_status/' + this.card_tasks[index].id, {
        status: 0
      })
              .then(response => {
                alert("Task was updated successfully");
                this.card_tasks[index].status = false;
              })
              .catch(function (error) {
                console.log(error.response);
              });
    },
    completeTask(index){
      axios.post('/task/update_status/' + this.card_tasks[index].id, {
        status: 1
      })
              .then(response => {
                alert("Task was updated successfully");
                this.card_tasks[index].status = true;
              })
              .catch(function (error) {
                console.log(error.response);
              });
    },
    deleteTask(index){
      if(confirm("Are you sure you want to delete this task?")) {
        axios.post('/task/delete/' + this.card_tasks[index].id)
                .then(response => {
                  toastr.success('<strong>Success!</strong> Task was deleted successfully.');

                  toastr.options.timeOut = 1000;
                  this.card_tasks.splice((index), 1);
                  /*$(".task_" + index).hide();*/
                })
                .catch(function (error) {
                  console.log(error.response);
                });
      }
    },
    deleteSubtask(index,sub_index){
      if(confirm("Are you sure you want to delete this subtask?")) {
        axios.post('/task/delete/' + this.card_tasks[index].sub_tasks[sub_index].id)
                .then(response => {
                  toastr.success('<strong>Success!</strong> Subtask was deleted successfully.');

                  toastr.options.timeOut = 1000;
                  this.card_tasks[index].sub_tasks.splice((sub_index), 1);
                  /*$(".task_" + index).hide();*/

                  if (this.card_tasks[index].sub_tasks.length === 0) {
                    this.card_tasks[index].add_sub_task = false;
                  }
                })
                .catch(function (error) {
                  console.log(error.response);
                });
      }
    },
    uncompleteSubtask(index,sub_index){
      axios.post('/task/update_status/' + this.card_tasks[index].sub_tasks[sub_index].id, {
        status: 0
      })
              .then(response => {
                alert("Task was updated successfully");
                this.card_tasks[index].status = false;
              })
              .catch(function (error) {
                console.log(error.response);
              });
    },
    completeSubtask(index,sub_index){
      axios.post('/task/update_status/' + this.card_tasks[index].sub_tasks[sub_index].id, {
        status: 1
      })
              .then(response => {
                alert("Task was updated successfully");
                this.card_tasks[index].status = true;
              })
              .catch(function (error) {
                console.log(error.response);
              });
    },
    saveVoice(index){
      /*console.log(index);*/
      var recording = document.getElementById('audioPlayer').src;
      var player = document.getElementById('audioFiles');
      var formData = new FormData();
      // var voice_recording = document.getElementById('audioPlayer');
      formData.append("audio", this.recordedAudio);
      formData.append("card_id", index);
      axios.post('/voice/record/' + index, formData,{
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        // card_id: index,
        // audio: this.recordedAudio,
      })
              .then(response => {
                toastr.success('<strong>Success!</strong> Voice recording was saved successfully.');

                toastr.options.timeOut = 1000;
                 this.recordings.push({card_id:response.data.recording.card_id,user:response.data.recording.user, recording:response.data.recording.recording})
              })
              .catch(function (error) {
                console.log(error.response);
              });
    },
    // callback (data) {
    //     console.debug(data)
    //   },
    onResult (data) {
      /*console.log('The recorded data:', data);
      console.log('Downloadable audio', window.URL.createObjectURL(data));*/
      document.getElementById('audioPlayer').src = window.URL.createObjectURL(data);
      this.recordedAudio = data;
    },
  }
}
</script>
<style scoped>
  .card-discussion{
    background:#ecf1f4;
    padding: 0.7rem  0.7rem 1.5rem 0.7rem;
    margin:0 -0.7rem;
  }

  .card-recordings{
    padding:1.5rem;
    margin:0 -1.5rem;
  }

  .view-card .card-body{
    padding: 0 0.7rem 0.7rem 0.7rem !important;
  }

.card-body{
  height: calc(100% - 9rem);
  overflow-y: auto;
  overflow-x: hidden;
  padding-bottom: 0px !important;
}

.view-card{
  position: absolute;
  right: 0px;
  top: 4.35rem;
  z-index: 10;
  background: #FFFFFF;
  overflow: auto;
  height: calc(100vh - calc(70px + 3rem));
}

.card-comments{
  background: #eae9e9;
  overflow: auto;
  width:100%;
  height: calc(100vh - calc(70px + 3rem));
}

  .subtask{
    width:100%;
    padding-left:1.5rem;
    margin:0px !important;
  }
  .addsubtask{
    width:100%;
    text-align: right;
    font-size: 0.75rem;
  }

  input.form-control-sm {
    height: 32px !important;
  }

  .mx-datepicker{
    width: 100% !important;
  }
</style>