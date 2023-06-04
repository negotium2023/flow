<template>
    <div>
        <div class="nav-client-board" style="overflow:initial;">
            <ul class="nav nav-tabs nav-fill">
                <li v-for="(board, index) in this.boards" class="nav-item">
                    <a v-if="!board.editBoardFlag" :href="'/workflows/'+board.id" class="nav-link" :class="board.id === board_id_global ? 'active' : ''">{{board.name}} <span class="dropdown" style="float: right;">
                            <a :id="index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:20px;padding:0px 10px;margin-left:5px;margin-right: -10px;">
                                <span><i class="fa fa-ellipsis-v"></i></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="index">
                                <a class="dropdown-item" href="javascript:void(0)" @click="editBoard(index,board.name)" style="color:#b5c9d4">Rename Board</a>
                                <a class="dropdown-item" href="javascript:void(0)" @click="deleteBoard(index,board.id)" style="color:#b5c9d4">Delete Board</a>
                            </div>
                        </span></a>
                    <span v-if="board.editBoardFlag" class="nav-link">
                        <input type="text" autocomplete="off" class="form-control form-control-sm" name="board"  style="background-color: #fff;min-width:250px;" v-on:keyup.13="updateBoard(index,board.id)" v-model="board_name" />
                    </span>
                </li>
<!--                <li class="nav-item">
                    <a :href="'/workflows/0'" class="nav-link">Archive</a>
                </li>-->
                <li class="nav-item">
                    <a v-if="!this.addBoardFlag" class="nav-link" style="cursor: pointer;" v-on:click="addBoard">
                        <i class="fa fa-plus" style="margin-top:8px;margin-right:10px;"></i> Add Board
                    </a>
                    <span v-if="this.addBoardFlag" class="nav-link">
                        <input type="text" autocomplete="off" class="form-control form-control-sm" name="board"  style="background-color: #fff;min-width:250px;" placeholder="Type new board name" v-on:focusout="saveBoard" v-on:keyup.13="saveBoard" v-model="board_name" />
                    </span>
                </li>
            </ul>
        </div>

        <div class="row workflow" style="position:relative;overflow-x: scroll; flex-wrap: nowrap;min-width:100%;margin:0px;padding:0px;height:calc(100vh - calc(94px + 3rem));" v-if="this.boards.length > 0">
            <div id="workoverflow" style="display: none;"  @click="hideCard()"></div>
            <div v-for="(section, index) in this.sections" class="col-lg-3 task-board">
                <div v-if="section.editFlag === 0 && section.updateFlag === 0" style="height: 3.7rem;">
                    <p v-if="section.name === 'Add Section'" :key="section.id" class="board-heading">
                        <span v-on:click="addSection(index, section.name)">
                            <i class="fa fa-plus"></i> {{section.name}}
                        </span>
                        <span class="dropdown" style="float: right;width:20px;">
                            <a v-if="section.name === 'Add Section'" :id="index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span><i class="fa fa-ellipsis-h"></i></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="index">
                                <a class="dropdown-item" href="javascript:void(0)" v-on:click="renameSection(index, section.name)" style="color:#4680a6">Rename Section</a>
                                <a class="dropdown-item" href="javascript:void(0)" v-on:click="deleteSection(index, section.id)" style="color:#b5c9d4">Delete Section</a>
                                <a class="dropdown-item" href="javascript:void(0)" @click="createTemplate(section)" :disabled="section.name === 'Add Section'" style="color:#4ebf85;border-top:1px solid #cccccc;">Create Card Template</a>
                                <a class="dropdown-item" href="javascript:void(0)" @click="createTemplateCard(section)" :disabled="section.name === 'Add Section'" style="color:#4ebf85;border-top:1px solid #cccccc;">Create Card From Template</a>
                                <a class="dropdown-item" href="javascript:void(0)" style="color:#4ebf85" @click="createCard(section)" :disabled="section.name === 'Add Section'">Create Card</a>
                                <a v-show="section.showArchived" class="dropdown-item" href="javascript:void(0)" v-on:click="toggleArchived(section)" style="color:#777777;border-top:1px solid #cccccc;">Hide Archived Cards</a>
                                <a v-show="!section.showArchived" class="dropdown-item" href="javascript:void(0)" v-on:click="toggleArchived(section)" style="color:#777777;border-top:1px solid #cccccc;">Show Archived Cards</a>
                            </div>
                        </span>
                    </p>
                    <p v-if="section.name !== 'Add Section'" :key="section.id" class="board-heading">{{section.name}}
                        <span class="dropdown" style="float: right;width:20px;">
                            <a v-if="section.name !== 'Add Section'" :id="index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span><i class="fa fa-ellipsis-h"></i></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="index">
                                <a class="dropdown-item" href="javascript:void(0)" v-on:click="renameSection(index, section.name)" style="color:#4680a6">Rename Section</a>
                                <a class="dropdown-item" href="javascript:void(0)" v-on:click="deleteSection(index, section.id)" style="color:#b5c9d4">Delete Section</a>
                                <a class="dropdown-item" href="javascript:void(0)" @click="createTemplate(section)" :disabled="section.name === 'Add Section'" style="color:#4ebf85;border-top:1px solid #cccccc;">Create Card Template</a>
                                <a class="dropdown-item" href="javascript:void(0)" @click="createTemplateCard(section)" :disabled="section.name === 'Add Section'" style="color:#4ebf85;border-top:1px solid #cccccc;">Create Card From Template</a>
                                <a class="dropdown-item" href="javascript:void(0)" style="color:#4ebf85" @click="createCard(section)" :disabled="section.name === 'Add Section'">Create Card</a>
                                <a v-show="section.showArchived" class="dropdown-item" href="javascript:void(0)" v-on:click="toggleArchived(section)" style="color:#777777;border-top:1px solid #cccccc;">Hide Archived Cards</a>
                                <a v-show="!section.showArchived" class="dropdown-item" href="javascript:void(0)" v-on:click="toggleArchived(section)" style="color:#777777;border-top:1px solid #cccccc;">Show Archived Cards</a>
                            </div>
                        </span>

                        <span class="dropdown" style="float: right;width:20px;">
                            <a v-if="section.name !== 'Add Section'" :id="index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span><i class="fa fa-info-circle"></i></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="index" style="width: 230px;">
                                <div class="dropdown-item">
                                    <table class="table table-sm table-borderless" style="margin-bottom: 0px;">
                                        <tr>
                                            <td>Upfront Revenue</td><td class="text-right" style="color:#4ebf85">R{{section.upfront_revenue}}</td>
                                        </tr>
                                        <tr>
                                            <td>Ongoing Revenue</td><td class="text-right" style="color:#4ebf85">R{{section.ongoing_revenue}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </span>
                    </p>
                </div>
                <div v-if="section.editFlag === 1" class="nav-link" data-toggle="modal">
                    <input type="text" autocomplete="off" name="board" class="form-control form-control-sm"  style="background-color: #fff;" :key="section.id" placeholder="Type new section name" v-on:focusout="saveSection(index, section.board_id)" v-on:keyup.13="saveSection(index, section.board_id)" v-model="section_name" />
                </div>
                <div v-if="section.updateFlag === 1" class="nav-link" data-toggle="modal">
                    <input type="text" autocomplete="off" name="board" class="form-control form-control-sm"  style="background-color: #fff;" :key="section.id" placeholder="Type new section name" v-on:focusout="updateSection(index, section.board_id, section.id)" v-on:keyup.13="updateSection(index, section.board_id, section.id)" v-model="section_name" />
                </div>


                    <draggable v-if="section.name !== 'Add Section'" :list="section.cards" animation="200" group="tasks" tag="ul" @change="onEnd(section.id, $event)" :id="'ul_list_'+section.id" :board-id="section.id" class="sortable connectedSortable" style="height:calc(100% - 3.7rem);overflow-y:auto;overflow-x:hidden;">
                      <li :task-id="card.id" v-for="card in section.cards" :class="'card card_'+card.id+' ui-sortable-handle '+(card.archived === '1' ? (section.showArchived === 0 ? 'd-none' : 'card-archived') : '')">
                          <div>
                              <div class="row card-content">
                                  <div class="col-md-10 pb-0" :key="card.id" @click="showCard(card, section.name)"><strong>{{card.name}}</strong></div>
                                  <div class="col-md-2 text-right pb-0"><span class="dropdown" style="float: right;width:20px;">
                                    <a :id="index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span><i class="fa fa-ellipsis-v"></i></span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="index">
                                        <a class="dropdown-item" href="javascript:void(0)" @click="copyCard(card,section)" style="color:#b5c9d4">Copy Card</a>
                                        <a class="dropdown-item" href="javascript:void(0)" @click="moveCard(card,section)" style="color:#b5c9d4">Move Card</a>
                                        <a class="dropdown-item" href="javascript:void(0)" v-if="card.archived === '0'" @click="archiveCard(card,section)" style="color:#b5c9d4">Archive Card</a>
                                        <a class="dropdown-item" href="javascript:void(0)" v-if="card.archived === '1'" @click="unarchiveCard(card,section)" style="color:#b5c9d4">Unarchive Card</a>
                                        <a class="dropdown-item" href="javascript:void(0)" @click="deleteCard(card,section)" style="color:#b5c9d4">Delete Card</a>
                                    </div>
                                </span></div>
                                  <div class="col-md-12 pt-0"><strong style="font-size: 0.75rem;">{{card.client_name}}</strong></div>
                                  <div class="col-md-12 m-0 p-0">
                                      <span class="badge" v-bind:style="{background: card.priority_status.fcolor}">{{card.priority_status.name}}</span>
                                      <span class="badge" v-bind:style="{background: card.status.fcolor}">{{card.status.name}}</span>
                                  </div>
                                  <div class="col-md-12 card-details">
                                      <i class="fas fa-user"></i> {{card.assigned_user.first_name + " " + card.assigned_user.last_name}}
                                  </div>
                                  <div class="col-md-8 card-details">
                                      <span class="text-danger"><i class="fas fa-calendar-alt"></i></span> {{card.due_date ? card.due_date.substring(0,10) : "N/A"}}
                                  </div>
                                  <div class="col-md-4 pull-right text-right card-details">
                                      <span class="text-info"><i class="fas fa-network-wired"></i></span> {{card.tasks.length}} Tasks
                                  </div>

                                  <div class="col-md-12 created_by">
                                      <small class="text-muted"><span class="float-left">{{card.creator.first_name + " " + card.creator.last_name}}</span><span class="float-right">{{card.created_at}}</span> </small>
                                  </div>
                              </div>
                          </div>
                      </li>
                        <li :task-id="-1000" :class="'card card_100000000'" style="border: none; !important; padding: 0px;">
                            <p class="add-card" v-if="section.cards.length > 0" style="margin: 0px; padding: 0px;">
                                <a href="javascript:void(0)" @click="createCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-4 float-left mr-1" style="margin-bottom: 0px;">Add Card <i class="fa fa-plus"></i></a>
                                <a href="javascript:void(0)" @click="createTemplateCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-6 float-right" style="max-width: 60%; margin-bottom: 0px;">Add Card from Template <i class="fa fa-plus"></i></a>
                            </p>

                            <p class="add-card" v-if="section.cards.length === 0" style="margin: 0px; padding: 0px;">
                                <!--<button :key="section.id" @click="createCard(section)" id="addTaskButton" type="button" class="btn btn-outline-primary" style="display: none;" >Add Card <i class="fa fa-plus"></i></button>-->
                                <a href="javascript:void(0)" v-if="section.name === 'Add Section'" class="btn btn-outline-primary col-md-4 float-left disabled" style="margin-bottom: 0px;">Add Card <i class="fa fa-plus"></i></a>
                                <a href="javascript:void(0)" v-if="section.name === 'Add Section'" class="btn btn-outline-primary col-md-7 float-right disabled" style="max-width: 60%; margin-bottom: 0px;">Add Card from Template <i class="fa fa-plus"></i></a>
                                <a href="javascript:void(0)" @click="createCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-4 float-left mr-1" style="margin-bottom: 0px;">Add Card <i class="fa fa-plus"></i></a>
                                <a href="javascript:void(0)" @click="createTemplateCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-7 float-right" style="max-width: 60%; margin-bottom: 0px;">Add Card from Template <i class="fa fa-plus"></i></a>
                            </p>
                        </li>
                    </draggable>

                    <!--TODO: find out how to disable drop zone and delete the code below-->

                    <ul v-if="section.name === 'Add Section'"  :id="'ul_list_'+section.id" :board-id="section.id" class="sortable connectedSortable"  style="max-height:65vh;overflow-y:auto;overflow-x:hidden;">
                      <li :task-id="card.id" v-for="card in section.cards" :class="'card card_'+card.id+' ui-sortable-handle'">
                        <div :key="card.id" @click="showCard(card, section.name)">
                          <div class="row card-content">
                            <div class="col-md-10 pb-0"><strong>{{card.name}}</strong></div>
                              <div class="col-md-2 text-right pb-0"><span class="dropdown" style="float: right;width:20px;">
                                    <a :id="index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span><i class="fa fa-ellipsis-v"></i></span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="index">
                                        <a class="dropdown-item" href="javascript:void(0)" v-if="card.archived === '0'" @click="archiveCard(card,section)" style="color:#b5c9d4">Archive Card</a>
                                        <a class="dropdown-item" href="javascript:void(0)" v-if="card.archived === '1'" @click="unarchiveCard(card,section)" style="color:#b5c9d4">Unarchive Card</a>
                                        <a class="dropdown-item" href="javascript:void(0)" @click="deleteCard(index,board.id)" style="color:#b5c9d4">Delete Card</a>
                                    </div>
                                </span></div>
                              <div class="col-md-12 pt-0"><strong style="font-size: 0.75rem;">{{card.client_name}}</strong></div>
                            <div class="col-md-12 m-0 p-0">
                              <span class="badge" v-bind:style="{background: card.priority_status.fcolor}">{{card.priority_status.name}}</span>
                              <span class="badge" v-bind:style="{background: card.status.fcolor}">{{card.status.name}}</span>
                            </div>
                            <div class="col-md-12 card-details">
                              <i class="fas fa-user"></i> {{card.assigned_user.first_name + " " + card.assigned_user.last_name}}
                            </div>
                            <div class="col-md-8 card-details">
                              <span class="text-danger"><i class="fas fa-calendar-alt"></i></span> {{card.due_date ? card.due_date.substring(0,10) : "N/A"}}
                            </div>
                              <div class="col-md-4 pull-right text-right card-details">
                                  <span class="text-info"><i class="fas fa-network-wired"></i></span> {{card.tasks_count ? card.tasks_count : card.tasks.length}} Tasks
                              </div>
                          </div>
                        </div>
                      </li>
                    </ul>

                <!--<p class="add-card" v-if="section.cards.length > 0">
                    &lt;!&ndash;<a href="javascript:void(0)" class="btn btn-outline-default col-md-4 float-left" >Empty <i class="fa fa-minus"></i></a>
                    &ndash;&gt;<a href="javascript:void(0)" @click="createCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-4 float-left mr-1" >Add Case <i class="fa fa-plus"></i></a>
                    <a href="javascript:void(0)" @click="createTemplateCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-7 float-right" >Add Case from Template <i class="fa fa-plus"></i></a>
                </p>
                <p class="add-card" v-if="section.cards.length === 0">
                    &lt;!&ndash;<button :key="section.id" @click="createCard(section)" id="addTaskButton" type="button" class="btn btn-outline-primary" style="display: none;" >Add Card <i class="fa fa-plus"></i></button>&ndash;&gt;
                    <a href="javascript:void(0)" v-if="section.name === 'Add Section'" class="btn btn-outline-primary col-md-4 float-left disabled" >Add Case <i class="fa fa-plus"></i></a>
                    <a href="javascript:void(0)" v-if="section.name === 'Add Section'" class="btn btn-outline-primary col-md-7 float-right disabled" >Add Case from Template <i class="fa fa-plus"></i></a>
                    <a href="javascript:void(0)" @click="createCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-4 float-left mr-1" >Add Case <i class="fa fa-plus"></i></a>
                    <a href="javascript:void(0)" @click="createTemplateCard(section)" v-if="section.name !== 'Add Section'" class="btn btn-outline-primary col-md-7 float-right" >Add Case from Template <i class="fa fa-plus"></i></a>
                </p>-->
                 <!--/.modal-->

                <div class="modal-mask" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);z-index:1000;" v-if="section.createTemplateModal" transition="modal" aria-hidden="true" role="dialog">
                    <div class="modal-dialog" style="margin-left:0px !important;">
                        <div class="modal-content" style="width:800px !important;min-width:800px !important;">
                            <div class="modal-header">

                                <span class="modal-title">Templates</span>
                                <button type="button" class="close" @click="hideTemplateModal(section)">&times;</button>
                            </div>
                            <div class="modal-body template-modal">
                                <div class="form-group col-md-12 m-0">
                                    <!--<select v-model="selected_template" class="w-100 form-control form-control-sm">
                                        <option value="" disabled hidden>Select Template</option>
                                        <option v-for="(value,key) in section.cards_templates" :value="value.id" >{{value.name}}</option>
                                    </select>-->
                                    <multiselect v-model="selected_template" placeholder="Select one" :options="section.cards_templates_dropdown" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
                                    </multiselect>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn btn-outline-primary btn-sm"  @click="createFromTemplate(section)">Add</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-mask" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);z-index:1000;" v-if="section.copyCardModal" transition="modal" aria-hidden="true" role="dialog">
                    <div class="modal-dialog" style="margin-left:0px !important;">
                        <div class="modal-content" style="width:800px !important;min-width:800px !important;">
                            <div class="modal-header">

                                <span class="modal-title">Copy Case</span>
                                <button type="button" class="close" @click="hideCopyCardModal(section)">&times;</button>
                            </div>
                            <div class="modal-body template-modal">
                                <div class="form-group col-md-12 m-0">
                                    <!--<select v-model="selected_template" class="w-100 form-control form-control-sm">
                                        <option value="" disabled hidden>Select Template</option>
                                        <option v-for="(value,key) in section.cards_templates" :value="value.id" >{{value.name}}</option>
                                    </select>-->
                                    <multiselect @input="getBoardSections(section,section.selected_board)" v-model="section.selected_board" placeholder="Select board" track-by="id" label="name" :options="section.boards_dropdown" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
                                    </multiselect>
                                </div>
                                <div class="form-group col-md-12 m-0">
                                    <!--<select v-model="selected_template" class="w-100 form-control form-control-sm">
                                        <option value="" disabled hidden>Select Template</option>
                                        <option v-for="(value,key) in section.cards_templates" :value="value.id" >{{value.name}}</option>
                                    </select>-->
                                    <multiselect @input="setSelectedSection(section,section.selected_section)" v-model="section.selected_section" placeholder="Select section" track-by="id" label="name" :options="section.section_dropdown" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
                                    </multiselect>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn btn-outline-primary btn-sm"  @click="saveCopyCard(section)">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-mask" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);z-index:1000;" v-if="section.moveCardModal" transition="modal" aria-hidden="true" role="dialog">
                    <div class="modal-dialog" style="margin-left:0px !important;">
                        <div class="modal-content" style="width:800px !important;min-width:800px !important;">
                            <div class="modal-header">

                                <span class="modal-title">Move Case</span>
                                <button type="button" class="close" @click="hideMoveCardModal(section)">&times;</button>
                            </div>
                            <div class="modal-body template-modal">
                                <div class="form-group col-md-12 m-0">
                                    <!--<select v-model="selected_template" class="w-100 form-control form-control-sm">
                                        <option value="" disabled hidden>Select Template</option>
                                        <option v-for="(value,key) in section.cards_templates" :value="value.id" >{{value.name}}</option>
                                    </select>-->
                                    <multiselect @input="getBoardSections(section,section.selected_board)" v-model="section.selected_board" placeholder="Select board" track-by="id" label="name" :options="section.boards_dropdown" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
                                    </multiselect>
                                </div>
                                <div class="form-group col-md-12 m-0">
                                    <!--<select v-model="selected_template" class="w-100 form-control form-control-sm">
                                        <option value="" disabled hidden>Select Template</option>
                                        <option v-for="(value,key) in section.cards_templates" :value="value.id" >{{value.name}}</option>
                                    </select>-->
                                    <multiselect @input="setSelectedSection(section,section.selected_section)" v-model="section.selected_section" placeholder="Select section" track-by="id" label="name" :options="section.section_dropdown" :searchable="true" :allow-empty="true" :multiple="false" :close-on-select="true">
                                    </multiselect>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn btn-outline-primary btn-sm"  @click="saveMoveCard(section)">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CreateCard
                v-show="creatingCard"
                :section="cardSection"
                v-on:new-task="addNewTask"
                v-on:close-me="hideCard"
        ></CreateCard>
        <CreateTemplate
                v-show="creatingTemplate"
                :section="cardSection"
                v-on:new-template="addNewTemplate"
                v-on:close-me="hideCard"
        ></CreateTemplate>
        <Card
            v-show="card"
            :card-details="card_details"
            v-on:new-task="addNewTask"
            v-on:update:name="updateCard"
            v-on:update:summary_description="updateCard"
            v-on:update:description="updateCard"
            v-on:update:client_name="updateCard"
            v-on:update:assignee="updateCard"
            v-on:update:duedate="updateCard"
            v-on:update:status="updateCard"
            v-on:update:priority="updateCard"
            v-on:update:tasks="updateCard"
            v-on:update:upfront_revenue="updateCard"
            v-on:update:due_date="updateCard"
            v-on:close-me="hideCard"
        />
    </div>
</template>

<script>
    import Dialog from 'primevue/dialog';
    import CreateTemplate from "./CreateTemplate";
    import CreateCard from "./CreateCard";
    import Card from "./Card";
    import draggable from 'vuedraggable';
    import Multiselect from 'vue-multiselect'


    export default {
        name: "Board",
        components: {
          'CreateTemplate': CreateTemplate,
          'CreateCard': CreateCard,
          'Card' : Card,
          'draggable': draggable,
            'multiselect': Multiselect
        },
        data: function () {
            return {
                serverURL: '',
                board_id: 0,
                boards: [{
                    editBoardFlag: false
                }],
                sections: [],
                cards_templates: [],
                card_templates_dropdown: '',
                selected_board: '',
                boards_dropdown: [],
                selected_section: '',
                section_dropdown: [],
                task_model: {
                    'name': '',
                    'due_date': '',
                    'section_id': 0,
                    'parent_id': 0, // Set this to the parent task, if a task is a child
                    'status_id': 0
                },
                addBoardFlag: false,
                addSectionFlag: false,
                updateSectionFlag: false,
                addCardFlag: false,
                board_name: '',
                section_name: '',
                show_alert: true,
                vueParameters: null,
                taskDropdown: [],
                cardSection: {
                    card_form:{
                        assignee_id: "",
                    },
                },
                creatingTemplate: false,
                creatingCard: false,
                createTemplateModal: false,
                copyCardModal: false,
                moveCardModal: false,
                selected_card: '',
                card: false,
                card_details: {},
                dragged: null,
                selected_template: '',
            }
        },
        props: [
            'parameters',
            'board_id_global'
        ],
        mounted(){
            this.serverURL = window.location.origin;
            this.vueParameters = JSON.parse(this.parameters);
            this.board_id = JSON.parse(JSON.stringify(this.vueParameters.board_id));
            this.sections = JSON.parse(JSON.stringify(this.vueParameters.sections));
            this.getBoards();
        },
        methods: {
            toggleArchived(section){
                if(section.showArchived === 1){
                    section.showArchived = 0;
                } else {
                    section.showArchived = 1;
                }
            },
            hoverd(index){
                if(this.boards[index].hover){
                    this.boards[index].hover = 0;
                } else {
                    this.boards[index].hover = 1;
                }
            },
            getBoards(){
                axios.get('/board')
                .then(response => {
                    this.boards = response.data;

                })
                .catch(error => {
                    toastr.error('<strong>Error!</strong> An unexpected error occured.');

                    toastr.options.timeOut = 1000;
                });
            },
            getSectionsWithTasks(){
              axios.get('/workflows')
                  .then(response =>{
                    this.vueParameters = response.data.cards;
                    /*console.log(response.data.cards);*/
                  })
            },
            addBoard(){
                this.addBoardFlag = true;
            },
            editBoard(index,boardName){
                this.boards[index].editBoardFlag = true;
                this.board_name = boardName;
            },
            addSection(index, sectionName){
                this.sections[index].editFlag = 1;
                this.section_name = '';
                this.addSectionFlag = true;
            },
            renameSection(index, sectionName){
                /*console.log('boo');*/
                this.sections[index].updateFlag = 1;
                this.section_name = sectionName;
                this.addSectionFlag = false;
            },
            addCard(){
                this.addCardFlag = true;
            },
            saveBoard(){
                // Todo: Add a loader
                if(this.addBoardFlag){
                    if(this.board_name === ''){
                        this.addBoardFlag = false;
                        return false;
                    }
                    axios.post('/board', {
                        'name': this.board_name
                    })
                    .then(response => {

                        toastr.success('<strong>Success!</strong> '+response.data.message);

                        toastr.options.timeOut = 1000;


                        // Get Boards
                        this.getBoards();
                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                        /*console.log(error);*/
                    });

                    this.addBoardFlag = false;
                }
            },
            saveSection(index, board_id){

                // Todo: Add a loader
                if(this.sections[index].editFlag === 1){
                    this.sections[index].editFlag = 0;
                    if(this.section_name === ''){
                        return false;
                    }
                    axios.post('/section', {
                        'name': this.section_name,
                        'board_id': board_id
                    })
                        .then(response => {
                            this.section_name = '';

                            this.sections.splice((this.sections.length - 1), 0,response.data.section);
                            // Add
                            toastr.success('<strong>Success!</strong> '+response.data.message);

                            toastr.options.timeOut = 1000;
                            //location.reload();
                            // Todo: don't refresh the page, write a function to get cards instead
                            // this.getCards();

                            this.sections[index].editFlag = 0;
                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/

                            this.sections[index].editFlag = 0;
                        });
                }
            },
            updateSection(index, board_id, section_id){

                // Todo: Add a loader
                if(this.sections[index].updateFlag === 1){
                    this.sections[index].updateFlag = 0;
                    this.sections[index].editFlag = 0;
                    if(this.section_name === ''){
                        return false;
                    }
                    axios.post('/section', {
                        'id': section_id,
                        'name': this.section_name,
                        'board_id': board_id
                    })
                        .then(response => {

                            this.sections[index].name = response.data.section.name;
                            this.section_name = response.data.section.name;
                            // Add
                            toastr.success('<strong>Success!</strong> '+response.data.message);

                            toastr.options.timeOut = 1000;

                            this.sections[index].updateFlag = 0;
                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error);*/

                            this.sections[index].updateFlag = 0;
                        });
                }
            },
            updateBoard(index, board_id){

                    axios.post('/board/update', {
                        'board_id': board_id,
                        'name': this.board_name
                    })
                        .then(response => {
                            this.boards[index].name = response.data.board.name;
                            this.board_name = response.data.board.name;

                            toastr.success('<strong>Success!</strong> '+response.data.message);

                            toastr.options.timeOut = 1000;

                            this.boards[index].editBoardFlag = false;

                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/
                        });

            },
            deleteBoard(index, board_id){

                if(confirm("Are you sure you want to delete this board. There are clients linked to these cards.")) {
                    axios.post('/board/delete', {
                        'board_id': board_id
                    })
                        .then(response => {
                            this.boards.splice((index), 1);
                            // Add
                            toastr.success('<strong>Success!</strong> '+response.data.message);

                            toastr.options.timeOut = 1000;
                            //location.reload();
                            // Todo: don't refresh the page, write a function to get cards instead
                            // this.getCards();
                            //window.location.href = '/workflows';
                            this.getBoards();

                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/
                        });
                }

            },
            copyCard(card,section){
                //this.copyCardModal = true;
                section.copyCardModal = true;
                section.selected_card = card.id;
                $('#workoverflow').show();
                axios.post('/board/getboards', {
                    'card_id': card.id,
                    'section_id' : section.id
                })
                    .then(response => {
                        section.boards_dropdown = response.data;
                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                        /*console.log(error.response);*/
                    });
            },
            moveCard(card,section){
                //this.copyCardModal = true;
                section.moveCardModal = true;
                section.selected_card = card.id;
                $('#workoverflow').show();
                axios.post('/board/getboards', {
                    'card_id': card.id,
                    'section_id' : section.id
                })
                    .then(response => {
                        section.boards_dropdown = response.data;
                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                        /*console.log(error.response);*/
                    });
            },
            hideCopyCardModal(section){
                $('#workoverflow').hide();
                section.selected_board = '';
                section.selected_section = '';
                section.selected_card = '';
                section.copyCardModal = !section.copyCardModal;
            },
            hideMoveCardModal(section){
                $('#workoverflow').hide();
                section.selected_board = '';
                section.selected_section = '';
                section.selected_card = '';
                section.moveCardModal = !section.moveCardModal;
            },
            getBoardSections(section,selected_board){
                axios.post('/board/getboardsections', {
                    'board': selected_board
                })
                    .then(response => {
                        section.section_dropdown = response.data;
                        //console.log(response.data);
                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                        /*console.log(error.response);*/
                    });
            },
            setSelectedSection(section,selected_section){
                section.selected_section = selected_section;
            },
            saveCopyCard(section){
                let board_name = section.selected_board;
                let section_name = section.selected_section;
                let card_id = section.selected_card;

                axios.post('/card/copy', {
                    'board': board_name,
                    'section': section_name,
                    'card': card_id
                })
                    .then(response => {
                        $('#workoverflow').hide();
                        section.selected_board = '';
                        section.selected_section = '';
                        section.selected_card = '';
                        section.copyCardModal = !section.copyCardModal;

                        if(response.data.board_id === section.board_id) {
                            this.sections.forEach((card, ind) => {
                                if (card.id === response.data.section_id){
                                    this.sections[ind].cards.push(response.data.card);
                                }
                            });
                        }
                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                        /*console.log(error.response);*/
                    });
            },
            saveMoveCard(section){
                let board_name = section.selected_board;
                let section_name = section.selected_section;
                let card_id = section.selected_card;

                axios.post('/card/move', {
                    'board': board_name,
                    'section': section_name,
                    'card': card_id
                })
                    .then(response => {

                        $(".card_" + card_id).remove();
                        $('#workoverflow').hide();
                        section.selected_board = '';
                        section.selected_section = '';
                        section.selected_card = '';
                        section.moveCardModal = !section.moveCardModal;

                        if(response.data.board_id === section.board_id) {
                            this.sections.forEach((card, ind) => {
                                if (card.id === response.data.section_id){
                                    this.sections[ind].cards.push(response.data.card);
                                }
                            });
                        }

                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                        /*console.log(error.response);*/
                    });
            },
            deleteCard(card,section_name){
                if(confirm("Are you sure you want to delete this card.")) {
                    axios.get('/card/delete/' + card.id, {
                        'card_id   ': card.id
                    })
                        .then(response => {
                            //this.sections[card.section_id].cards.splice((card), 1);
                            $(".card_" + card.id).hide();
                            // Add
                            toastr.success('<strong>Success!</strong> Card successfully deleted.');

                            toastr.options.timeOut = 1000;

                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/
                        });
                }

            },
            archiveCard(card,section_name){

                if(confirm("Are you sure you want to archive this card.")) {
                    axios.get('/card/archive/' + card.id, {
                        'card_id   ': card.id
                    })
                        .then(response => {
                            //this.sections.cards.splice((card.id), 1);
                            if(this.section_name.showArchived) {
                                $(".card_" + card.id).hide();
                                // Add
                            }
                            card.archived = '1';
                            toastr.success('<strong>Success!</strong> Card successfully archived');

                            toastr.options.timeOut = 1000;

                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/
                        });
                }

            },
            unarchiveCard(card,section_name){

                if(confirm("Are you sure you want to unarchive this card.")) {
                    axios.get('/card/unarchive/' + card.id, {
                        'card_id   ': card.id
                    })
                        .then(response => {
                            //this.sections.cards.splice((card.id), 1);
                            if(this.section_name.showArchived) {
                                $(".card_" + card.id).hide();
                                // Add
                            }
                            card.archived = '0';
                            toastr.success('<strong>Success!</strong> Card successfully unarchived');

                            toastr.options.timeOut = 1000;
                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/
                        });
                }

            },
            deleteSection(index, section_id){

                    axios.post('/section/delete', {
                        'section_id': section_id
                    })
                        .then(response => {
                            this.sections.splice((index), 1);
                            // Add
                            toastr.success('<strong>Success!</strong> '+response.data.message);

                            toastr.options.timeOut = 1000;

                            //location.reload();
                            // Todo: don't refresh the page, write a function to get cards instead
                            // this.getCards();

                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                            /*console.log(error.response);*/
                        });

            },
            addTask(section_id){
                this.task_model.status_id = section_id; // For the task to be added to the correct section
                document.getElementById('addTaskButton').click();
            },
            saveTask(){
                // Todo: Add a loader
                if(this.task_model.name === ''){
                    alert('Task name can not be empty')
                    return false;
                }
                axios.post('/task', {
                    'name': this.task_model.name,
                    'due_date': this.task_model.due_date,
                    'parent_id': this.task_model.parent_id,
                    'status_id': this.task_model.status_id // This is the section id of the task
                })
                .then(response => {
                    /*console.log('response', response);*/
                    this.task_model.name = '';
                    this.task_model.due_date = '';
                    this.task_model.parent_id = 0;
                    this.task_model.status_id = 0;
                    // Add
                    toastr.success('<strong>Success!</strong> '+response.data.message);

                    toastr.options.timeOut = 1000;
                    document.getElementById('closeAddTaskModal').click();
                    location.reload();
                    // Todo: don't refresh the page, write a function to get cards instead
                    // this.getCards();
                })
                .catch(error => {
                    toastr.error('<strong>Error!</strong> An unexpected error occured.');

                    toastr.options.timeOut = 1000;
                    /*console.log(error);*/

                    document.getElementById('closeAddTaskModal').click();
                });
            },
            createTemplate(section){
                /*console.log(section);*/


                    $('#workoverflow').show();
                    this.cardSection = {
                        name: section.name,
                        section_id: section.id,
                        card_form:{
                            assignee_id: "",
                            assignee_name: "",
                            assignee: "",
                            progress_status_id: "1",
                            priority_status_id: "",
                            team_ids: [],
                            team_names: [],
                            open: false,
                            open2: false,
                            open3: false,
                            open4: false,
                            open5: false,
                            open6: false,
                        },
                        tasks: [],
                        priority_status: []
                    };

                /*console.log(this.cardSection);*/

                    this.creatingTemplate = !this.creatingTemplate;

            },
            createCard(section){
                if(section.name !== "Add Section"){

                    $('#workoverflow').show();
                    this.cardSection = {
                        name: section.name,
                        section_id: section.id,
                        card_form:{
                            name: "",
                            deadline: "",
                            assignee_id: "",
                            assignee_name: "",
                            assignee: "",
                            progress_status_id: "1",
                            priority_status_id: "",
                            team_ids: [],
                            team_names: [],
                            saved:0,
                            selected_deadline:'',
                            open: false,
                            open2: false,
                            open3: false,
                            open4: false,
                            open5: false,
                            open6: false,
                        },
                        tasks: [],
                        priority_status: [],
                        discussions:[],
                    };

                    this.creatingCard = true
                }
            },
            createTemplateCard(section){
                if(section.cards_templates.length > 0) {
                    axios.get('/cardtemplates/' + section.id)
                        .then(response => {
                            console.log(response.data);
                            $('#workoverflow').show();
                            section.createTemplateModal = true;
                            section.cards_templates_dropdown = response.data;
                            /*section.cards_templates.$forceUpdate();*/

                        })
                        .catch(error => {
                            toastr.error('<strong>Error!</strong> An unexpected error occured.');

                            toastr.options.timeOut = 1000;
                        });
                } else {
                    toastr.warning('<strong>Warning!</strong> No templates for this section');

                    toastr.options.timeOut = 1000;

                }
            },
            hideTemplateModal(section){
                $('#workoverflow').hide();
                section.createTemplateModal = false;
            },
            createFromTemplate(section){
                let templateid = this.selected_template;

                axios.get('/cardtemplates/show/' + templateid)
                    .then(response => {

                        console.log(response.data);
                        section.createTemplateModal = false;
                        this.selected_template = '';

                        $('#workoverflow').show();
                        this.cardSection = {
                            hasErrors:false,
                            name: section.name,
                            section_id: section.id,
                            card_form: response.data,
                            tasks: response.data.tasks,
                            priority_status: response.data.priority_status,
                            discussions:[],
                        };
                        this.cardSection.card_form.open = false;
                        this.creatingCard = true
                    })
                    .catch(error => {
                        toastr.error('<strong>Error!</strong> An unexpected error occured.');

                        toastr.options.timeOut = 1000;
                    });
            },
            addNewTask (task){
                $('#workoverflow').hide();
              this.creatingCard = false;
              this.sections.forEach((card, ind) => {
                if (card.id === task.section_id){
                  this.sections[ind].cards.push(task)
                }
              });
                /*this.cardSection = {};*/

            },
            addNewTemplate (task){
                $('#workoverflow').hide();
              this.creatingTemplate = false;
                /*this.sections.forEach((card, ind) => {
                    if (card.id === task.section_id){
                        console.log(task);
                        this.sections[ind].cards_templates.push('')
                    }
                });*/
                /*this.cardSection = {};*/

            },
            hideCard(){
                $('#workoverflow').hide();
                if(this.card) {
                    this.card = false;
                }
                if(this.creatingCard){
                    this.creatingCard = false;
                }
                if(this.creatingTemplate){
                    this.creatingTemplate = false;
                }
                this.sections.forEach((card, ind) => {
                            this.sections[ind].createTemplateModal = false;
                        });
            },
            showCard(card, section_name){
                /*console.log(card);*/
                $('#workoverflow').show();
              this.card_details = card;
              this.card_details["section_name"] = section_name;
              this.card_details["tasks"].forEach((task, ind) => {
                    task.name = this.card_details.tasks[ind].name;
                    task.due_date = this.card_details.tasks[ind].due_date;
                    task.open = false;
                    task.add_sub_task = false;
                    task.add_deadline = false;
                    task.assign_user = false;
                });
              this.card = !this.card;
            },
            updateCard(updated_card, field){
              this.sections.forEach((section, index) =>{
                if (section.id == updated_card.section_id){
                  section.cards.forEach((card, ind) =>{
                      if ((card.id === updated_card.id) && (field === "name")){
                          card.name = updated_card.name;
                      }

                      if ((card.id === updated_card.id) && (field === "summary_description")){
                          card.summary_description = updated_card.summary_description;
                      }

                      if ((card.id === updated_card.id) && (field === "description")){
                          card.description = updated_card.description;
                      }

                      if ((card.id === updated_card.id) && (field === "client_name")){
                          card.client_name = updated_card.client_name;
                      }

                      if ((card.id === updated_card.id) && (field === "upfront_revenue")){
                          card.upfront_revenue = updated_card.upfront_revenue;
                      }

                      if ((card.id === updated_card.id) && (field === "ongoing_revenue")){
                          card.ongoing_revenue = updated_card.ongoing_revenue;
                      }

                      if ((card.id === updated_card.id) && (field === "due_date")){
                          card.due_date = updated_card.due_date;
                      }

                    if ((card.id == updated_card.id) && (field === "assignee")){
                      card.assigned_user.first_name = updated_card.assigned_user.first_name;
                      card.assigned_user.last_name = updated_card.assigned_user.last_name;
                    }

                    if ((card.id == updated_card.id) && (field === "status")){
                      card.status.name = updated_card.status.name;
                      card.status.fcolor = updated_card.status.fcolor;
                    }

                    if ((card.id == updated_card.id) && (field === "priority")){
                      card.priority_status.name = updated_card.priority_status.name;
                      card.priority_status.fcolor = updated_card.priority_status.fcolor;
                    }

                      if ((card.id == updated_card.id) && (field === "duedate")){
                          card.due_date = updated_card.due_date;
                      }

                      if ((card.id == updated_card.id) && (field === "tasks")){
                          card.tasks = updated_card.tasks;
                          card.tasks.forEach((task, ind) => {
                              task.open = '';
                              task.selected_duedate = updated_card.tasks[ind].due_date;
                              task.due_date = updated_card.tasks[ind].due_date;
                              task.add_sub_task = updated_card.tasks[ind].add_sub_task;
                              task.add_deadline = updated_card.tasks[ind].add_deadline;
                              task.assign_user = updated_card.tasks[ind].assign_user;
                              task.assignee = updated_card.tasks[ind].assignee_name;
                          });
                          /*console.log(card.tasks);*/
                      }
                  })
                }
              })
            },
            onEnd(section_id, event){
              if (event.hasOwnProperty('added')){
                      /*console.log(event.added.element);*/

                      axios.patch('/card/' + event.added.element.id, {
                          section_id: section_id
                      }).then(response => {
                          if(confirm("Would you like to set all the tasks connected to this card as complete?")) {
                              axios.get('/card/complete_tasks/' + event.added.element.id, {
                                  section_id: section_id
                              }).then(response => {
                                  console.log(response.data);
                                  this.getBoards();
                              }).catch(err => console.log(err))
                          }
                          /*console.log(response.data);*/
                      }).catch(err => console.log(err))

              }

            },
          checkMove(e, section_id){
            console.log(e)
          }
        }
    }
</script>

<style>
    card{
      overflow-y: auto!important;
      cursor: move;
    }
    .chat-initials {
        padding: 5px;
        border-radius: 25px;
        color: #ffffff;
        cursor: pointer;
    }

    table{
        border-collapse: separate;
        border-spacing: 1px !important; /* Apply cell spacing */
    }
    table, th, td{
        /*border: 1px solid #666;*/
    }
    table th, table td{
        padding: 2px !important; /* Apply cell padding */
    }

    .workflow .modal-dialog {
        width: 100%;
        height: 100%;
        margin-left: 280px;
        /*margin: 0;
        padding: 0;*/
    }

    .workflow .modal-content {
        height: auto;
        min-height: 90%;
        min-width: 90% !important;
        width: 1200px !important;
        border-radius: 0;
    }

    .board-heading {
        font-weight: bold !important;
        margin: 10px;
    }

    .add-card {
        margin: 10px;
    }

    .add-card-link{
        cursor: pointer;
    }

    .task-board {
        background-color: #E2E4E6;;
        margin: 10px;
        min-width: 400px;
        border-radius: 5px;
        height: calc(100% - 24px);
    }

    .card{
        background-color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    .task-type {
        text-align: right;
        color: #0e90d2;
        font-size: 12px;
        font-weight: bold;
    }

    .sortable {
        margin: 0;
        padding: 2px;
        border-radius: 5px;
    }

    .sortable li {
        margin: 3px 3px 3px 3px;
        padding: 0.5em;
    }

    .ui-draggable, .ui-droppable {
        background-position: top;
    }

    .planning-btn {
        cursor: pointer;
    }

    .planning-btn:hover {
        cursor: pointer;
        color: #0d88c1;
    }

    span.multiselect__option--highlight::after{
        display:none !important;
    }

    .multiselect--active{

    }
</style>