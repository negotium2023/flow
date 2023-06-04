<template>
        <ul class="list-group w-100">
            <li v-for="(activity, activity_index) in activities" class="list-group-item">
                <div class="form-row">
                    <div class="row col-md-8 mb-0 pb-0" v-if="activity.type!=='heading' && activity.type!=='subheading' && activity.type!=='content'">
                        <label class="col-md-1 d-table-cell" style="justify-content: left;vertical-align:top;">Label</label>
                        <div class="col-md-11 pl-0 pr-0 d-table-cell" style="justify-content: left;vertical-align:top;">
                            <input type="hidden" :name="'activities['+activity_index+'][id]'" class="form-control form-control-sm" v-model="activity.id" required/>
                            <textarea-autosize
                                    :name="'activities[' + activity_index + '][name]'"
                                    placeholder="Activity Name"
                                    v-model="activity.name"
                                    rows="1"
                                    :max-height="350"
                                    @blur.native="onBlurTextarea"
                                    class="form-control form-control-sm d-inline m-0"
                                    style="width: 85%"
                                    required
                            ></textarea-autosize>
                            <button type="button" title="Activity label/text style"  @click="showStylesModal(activity_index)" class="btn btn-success btn-sm" style="justify-content: left;vertical-align:top;" :disabled="activity.report == 1"><i class="fa fa-fw fa-paint-brush"></i></button><br />
                            <small class="text-muted"><i class="fas fa-info-circle"></i>&nbsp;Press Shift + Enter for a new line</small>
                        </div>
                    </div>
                    <div class="row col-md-8 mb-0 pb-0" v-if="activity.type==='heading' || activity.type==='subheading' || activity.type==='content'">
                        <label class="col-md-1 d-table-cell" style="justify-content: left;vertical-align:top;">Text</label>
                        <div class="col-md-11 pl-0 pr-0 d-table-cell" style="justify-content: left;vertical-align:top;">
                            <input type="hidden" :name="'activities['+activity_index+'][id]'" class="form-control form-control-sm" v-model="activity.id" required/>
                            <input type="hidden" :name="'activities['+activity_index+'][type]'" class="form-control form-control-sm" v-model="activity.type" required/>
                            <textarea-autosize
                                    :name="'activities[' + activity_index + '][name]'"
                                    placeholder="Heading"
                                    v-model="activity.name"
                                    rows="1"
                                    :max-height="350"
                                    @blur.native="onBlurTextarea"
                                    class="form-control form-control-sm d-inline"
                                    style="width: 85%;"
                                    required
                            ></textarea-autosize>
                            <button type="button" title="Activity label/text style"  @click="showStylesModal(activity_index)" class="btn btn-success btn-sm" style="justify-content: left;vertical-align:top;" :disabled="activity.report == 1"><i class="fa fa-fw fa-paint-brush"></i></button><br />
                            <small class="text-muted"><i class="fas fa-info-circle"></i>&nbsp;Press Shift + Enter for a new line</small>
                        </div>
                    </div>
                    <div class="row col-md-3 mb-0 pb-0">
                        <label class="col-md-2 pr-0" style="justify-content: left;">Type</label>
                        <div class="col-md-10 pl-0 pr-0">
                            <select :name="'activities['+activity_index+'][type]'" class="form-control form-control-sm" v-model="activity.type" style="width:90%;">
                                <option v-for="actionableType in actionableTypes" :value="actionableType.value">{{actionableType.text}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row" v-if="activity.type==='heading' || activity.type==='subheading' || activity.type==='content'">
                    <div class="row col-md-12">
                        <label class="col-md-1 d-table-cell" style="justify-content: left;vertical-align:top;">Content Text</label>
                        <div class="col-md-11 pl-0 pr-0 d-table-cell" style="justify-content: left;vertical-align:top;">
                            <button v-show="!activity.is_text_content_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleContentTextShown(activity_index)"><i class="fa fa-eye"></i> Show Content Text</button>
                            <button v-show="activity.is_text_content_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleContentTextShown(activity_index)"><i class="fa fa-eye-slash"></i> Show Content Text</button>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-0 pt-0" v-show="activity.is_text_content_shown">
                        <tinymce-editor api-key="361nrfmxzoobhsuqvaj3hyc2zmknskzl4ysnhn78pjosbik2" :name="'activities['+activity_index+'][text_content]'"  v-model="activity.text_content" :init="{content_style: 'body { font-family: Arial; }',inline_styles:'',height:'200px',width:'100%',menubar:'',plugins: 'wordcount advlist lists table',branding:false,toolbar:'undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'}" ></tinymce-editor>
                    </div>
                </div>
                <div class="form-row">
                    <div class="row col-md-8 pt-0 pb-0 mb-0">
                        <label class="label col-md-12">Attributes</label>
                    </div>
                    <div class="row col-md-4 pt-0 pb-0 mb-0">
                        <label class="label col-md-12">Properties</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="row col-md-8 pt-0 pb-0 mb-0">
                        <div class="form-group col-md-1">
                            <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Is the activity a key performance indicator required for step completion?">
                                <input type="checkbox" :name="'activities['+activity_index+'][kpi]'" class="custom-control-input" v-model="activity.kpi" :id="'kpi-' + activity_index"/>
                                <label class="custom-control-label" :for="'kpi-' + activity_index">KPI</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Is commenting allowed on the activity?">
                                <input type="checkbox" :name="'activities['+activity_index+'][comment]'" class="custom-control-input" v-model="activity.comment" :id="'comment-' + activity_index"/>
                                <label class="custom-control-label" :for="'comment-' + activity_index">Comments</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Is the activity a procedure?">
                                <input type="checkbox" :name="'activities['+activity_index+'][procedure]'" class="custom-control-input" v-model="activity.procedure" :id="'procedure-' + activity_index"/>
                                <label class="custom-control-label" :for="'procedure-' + activity_index">Procedure</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Is the activity a value?">
                                <input type="checkbox" :name="'activities['+activity_index+'][value]'" class="custom-control-input" @click="checkValue(activity_index)" v-model="activity.value" :id="'value-' + activity_index"/>
                                <label class="custom-control-label" :for="'value-' + activity_index">Value</label>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div>
                                <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Is the activity part of the Client Basket?">
                                    <input type="checkbox" :name="'activities['+activity_index+'][client_bucket]'" class="form-inline custom-control-input" v-model="activity.client_bucket" :id="'client_bucket-' + activity_index"/>
                                    <label class="custom-control-label" :for="'client_bucket-' + activity_index">Default to Client Basket</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4" v-if="activity.type==='dropdown'">
                            <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Should this activity allow multiple selections?">
                                <input type="checkbox" :name="'activities['+activity_index+'][multiple_selection]'" class="custom-control-input" v-model="activity.multiple_selection" :id="'multiple_selection-' + activity_index"/>
                                <label class="custom-control-label" :for="'multiple_selection-' + activity_index">Allow Multiple Selections</label>
                            </div>
                        </div>
                        <div class="form-group col-md-2" v-if="activity.type==='date'">
                            <div class="custom-control custom-checkbox custom-control-inline ml-2" title="Should this activity accept a future date?">
                                <input type="checkbox" :name="'activities['+activity_index+'][future_date]'" class="custom-control-input" v-model="activity.future_date" :id="'future_date-' + activity_index"/>
                                <label class="custom-control-label" :for="'future_date-' + activity_index">Future Date</label>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-4 pt-0 pb-0 mb-0">
                        <div class="form-group col-md-3">
                            <div class="form-inline" style="margin-top:-7px;">
                                <label class="pr-1">Indent</label>
                                <select :name="'activities['+activity_index+'][level]'" class="form-control form-control-sm col-md-6 pt-0 pb-0" v-model="activity.level" title="Position" :disabled="activity.report == 1">
                                    <option v-for="levelType in levelTypes" :value="levelType.value">{{levelType.text}}</option>
                                </select>
                            </div>
                        </div>

                        <!-- <div class="form-group col-md-5">
                            <div class="form-inline" style="margin-top:-7px;">
                                <label class="pr-1">Position</label>
                                <select :name="'activities['+activity_index+'][position]'" class="form-control form-control-sm col-md-8 pt-0 pb-0" v-model="activity.position" title="Position" :disabled="activity.report == 1">
                                    <option v-for="positionType in positionTypes" :value="positionType.value">{{positionType.text}}</option>
                                </select>
                            </div>
                        </div> -->
                        <!-- <div class="form-group col-md-1">
                            <input type="hidden" :name="'activities['+activity_index+'][color]'" :value="activity.color"/>
                            <verte class="mt-0" picker="square" v-model="activity.color" @click="colorChange(activity_index)"></verte>
                        </div> -->
                        <div class="form-group col-md-3">
                            <div class="form-inline" style="margin-top:-7px;">
                                <label class="pr-1">Mapped Field</label>
                                <select :name="'activities['+activity_index+'][mapped_field]'" class="form-control form-control-sm" v-model="activity.mapped_field" title="Grouping type">
                                    <option v-for="(field, field_index) in mappedFields">{{field}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-inline" style="margin-top:-7px;" v-show="is_grouping_items_shown">
                                <label class="pr-1">Group</label>
                                <select :name="'activities['+activity_index+'][grouping_value]'" class="form-control form-control-sm" v-model="activity.grouping_value" title="Grouping type" :disabled="activity.report == 1">
                                    <option v-for="groupingType in groupingTypes" :value="groupingType.value">{{groupingType.text}}</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="form-row pb-2" v-if="activity.type==='videoyoutube'">
                    <div class="col-md-12 mb-3">
                        <div class="form-inline">
                            <label class="control-label pr-2" :for="'show_tooltip-' + activity_index">Youtube embed code:</label>
                            <input type="text" :name="'activities['+activity_index+'][default_value]'" class="form-control form-control-sm d-inline mt-1" size="100" v-model="activity.default_value"/>
                        </div>
                    </div>
                </div>
                <div class="form-row pb-2" v-if="activity.type==='videoupload'">
                    <div style="padding-left:12px;">
                        <div class="form-inline">
                            <v-file-input
                                    v-model="activity.files"
                                    small-chips
                                    show-size
                                    multiple
                                    clearable
                                    accept=".mp4"
                                    loading="true"
                                    prepend-icon="mdi-video"
                                    clear-icon="mdi-delete"
                                    @change="videoChanged(activity_index)"
                            >
                            </v-file-input>
                            <input type="hidden" :name="'activities['+activity_index+'][default_value]'" class="form-control form-control-sm d-inline mt-1" v-model="activity.default_value"/>
                            <input type="hidden" :name="'activities['+activity_index+'][old_value]'" class="form-control form-control-sm d-inline mt-1" v-model="activity.old_value"/>
                        </div>
                    </div>
                </div>
                <div class="form-row pb-2" v-if="activity.type==='imageupload'">
                    <div class="row col-md-8" style="padding-left:12px;">
                        <div class="form-inline col-md-12">
                            <v-file-input
                                    v-model="activity.files"
                                    small-chips
                                    show-size
                                    multiple
                                    clearable
                                    accept=".jpg,.gif,.png"
                                    loading="true"
                                    prepend-icon="mdi-camera"
                                    clear-icon="mdi-delete"
                                    @change="imageChanged(activity_index)"
                            >
                            </v-file-input>
                            <input type="hidden" :name="'activities['+activity_index+'][default_value]'" class="form-control form-control-sm d-inline mt-1" v-model="activity.default_value"/>
                            <input type="hidden" :name="'activities['+activity_index+'][old_value]'" class="form-control form-control-sm d-inline mt-1" v-model="activity.old_value"/>
                        </div>
                    </div>
                    <div class="row col-md-4">
                        <div class="form-group col-md-4">
                            <div class="form-inline" style="margin-top:-7px;">
                                <label class="pr-1">Height</label>
                                <input type="text" :name="'activities['+activity_index+'][height]'" class="form-control form-control-sm col-md-7 p-1" v-model="activity.height">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-inline" style="margin-top:-7px;">
                                <label class="pr-1">Width</label>
                                <input type="text" :name="'activities['+activity_index+'][width]'" class="form-control form-control-sm col-md-7 p-1" v-model="activity.width">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <div class="form-inline" style="margin-top:-7px;">
                                <label class="pr-1">Align</label>
                                <select :name="'activities['+activity_index+'][alignment]'" placeholder="Select Process" v-model="activity.alignment" class="form-control form-control-sm">
                                    <option value="" disabled hidden>Please Select</option>
                                    <option v-for="alignmentType in alignmentTypes" :value="alignmentType.value">{{alignmentType.text}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row pb-2" v-if="activity.type==='dropdown'">
                    <div>
                        <button v-show="!activity.is_dropdown_items_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleDropdownItemShown(activity_index)"><i class="fa fa-eye"></i> Show dropdown items</button>
                        <button v-show="activity.is_dropdown_items_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleDropdownItemShown(activity_index)"><i class="fa fa-eye-slash"></i> Hide dropdown items</button>
                    </div>
                    <div v-show="activity.is_dropdown_items_shown">
                        <div v-for="(dropdownItem, dropdown_item_index) in activity.dropdown_items" class="d-inline">
                            <button type="button" class="btn btn-sm btn-secondary mt-1 mr-2" @click="deleteDropdownItem(activity_index, dropdown_item_index)">
                                {{dropdownItem}} <i class="fa fa-trash"></i>
                            </button>
                            <input type="hidden" :name="'activities['+activity_index+'][dropdown_items][]'" :value="dropdownItem"/>
                        </div>
                        <input type="text" class="form-control form-control-sm d-inline mt-1" v-model="activity.dropdown_item" @keydown.enter.prevent="createDropdownItem(activity_index)" placeholder="Dropdown Item label"/>
                    </div>
                </div>
                <div class="form-row">

                    <div class="form-group col-md-6" v-if="activity.type==='template_email' || activity.type==='document_email' || activity.type==='multiple_attachment' || activity.type==='textarea'">
                        <div>
                            <button v-show="!activity.is_tooltip_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleTooltipShown(activity_index)"><i class="fa fa-eye"></i> Show Guidance Note</button>
                            <button v-show="activity.is_tooltip_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleTooltipShown(activity_index)"><i class="fa fa-eye-slash"></i> Hide Guidance Note</button>
                            <div class="custom-control custom-checkbox custom-control-inline ml-2">
                                <input type="checkbox" :name="'activities['+activity_index+'][show_tooltip]'" class="form-inline custom-control-input" v-model="activity.show_tooltip" :id="'show_tooltip-' + activity_index"/>
                                <label class="custom-control-label" :for="'show_tooltip-' + activity_index">Show on activity</label>
                            </div>
                        </div>
                        <div class="col-sm-12" v-show="activity.is_tooltip_shown">
                            <div class="d-inline col-sm-12 mt-0 pt-0">
                                <tinymce-editor api-key="361nrfmxzoobhsuqvaj3hyc2zmknskzl4ysnhn78pjosbik2" :name="'activities['+activity_index+'][tooltip]'"  v-model="activity.default_value" :init="{inline_styles:'',height:'200px',width:'100%',menubar:'',plugins: 'wordcount advlist lists table',branding:false,toolbar:'undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'}" ></tinymce-editor>

                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12" v-if="activity.type!=='template_email' && activity.type!=='document_email' && activity.type!=='multiple_attachment' && activity.type!=='textarea'">
                        <div>
                            <button v-show="!activity.is_tooltip_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleTooltipShown(activity_index)"><i class="fa fa-eye"></i> Show Guidance Note</button>
                            <button v-show="activity.is_tooltip_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleTooltipShown(activity_index)"><i class="fa fa-eye-slash"></i> Hide Guidance Note</button>
                            <div class="custom-control custom-checkbox custom-control-inline ml-2">
                                <input type="checkbox" :name="'activities['+activity_index+'][show_tooltip]'" class="form-inline custom-control-input" v-model="activity.show_tooltip" :id="'show_tooltip-' + activity_index"/>
                                <label class="custom-control-label" :for="'show_tooltip-' + activity_index">Show on activity</label>
                            </div>
                        </div>
                        <div class="col-sm-12" v-show="activity.is_tooltip_shown">
                            <div class="d-inline col-sm-12 mt-0 pt-0">
                                <tinymce-editor api-key="361nrfmxzoobhsuqvaj3hyc2zmknskzl4ysnhn78pjosbik2" :name="'activities['+activity_index+'][tooltip]'"  v-model="activity.default_value" :init="{inline_styles:'',height:'200px',width:'100%',menubar:'',plugins: 'wordcount advlist lists table',branding:false,toolbar:'undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'}" ></tinymce-editor>

                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6" v-if="activity.type==='template_email' || activity.type==='document_email' || activity.type==='multiple_attachment' || activity.type==='textarea'">
                        <div>
                            <div>
                                <button v-show="!activity.is_default_value_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleDefaultValueShown(activity_index)"><i class="fa fa-eye"></i> Show Default Value</button>
                                <button v-show="activity.is_default_value_shown" class="btn btn-secondary-outline btn-sm" type="button" @click="toggleDefaultValueShown(activity_index)"><i class="fa fa-eye-slash"></i> Hide Default Value</button>
                                <div class="custom-control custom-checkbox custom-control-inline ml-2">
                                    <input type="checkbox" :name="'activities['+activity_index+'][use_default_value]'" @click="checkEmail(activity_index)" class="custom-control-input" v-model="activity.use_default_value" :id="'use_default_value-' + activity_index"/>
                                    <label class="custom-control-label" :for="'use_default_value-' + activity_index">   Use Default Value</label>
                                </div>
                            </div>
                        </div>
                        <div v-show="activity.is_default_value_shown" class="col-md-12 pb-1" v-if="activity.type==='textarea'">
                            <div class="d-inline col-sm-12 pt-0 mt-0">
                                <tinymce-editor api-key="361nrfmxzoobhsuqvaj3hyc2zmknskzl4ysnhn78pjosbik2" :name="'activities['+activity_index+'][default_value]'"  v-model="activity.default_value" :init="{inline_styles:'',height:'200px',width:'100%',menubar:'',plugins: 'wordcount advlist lists table',branding:false,toolbar:'undo redo | fontselect fontsizeselect formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'}" ></tinymce-editor>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3" v-if="activity.type==='template_email' || activity.type==='document_email' || activity.type==='multiple_attachment'">
                            <div v-show="activity.is_default_value_shown" class="form-inline">
                                <input type="text" :name="'activities['+activity_index+'][default_value]'" class="form-control form-control-sm d-inline mt-1" size="50" v-model="activity.default_value"/>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="form-row" v-if="activity.id != 0">
                    <strong>ID: </strong>{{activity.id}}
                </div>

                <div class="modal-mask" v-show="activity.show_styles" transition="modal" aria-hidden="true" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content" style="width:700px;">
                            <div class="modal-header">

                                <span class="modal-title">Activity Font Style</span>
                                <button type="button" class="close"  @click="hideStylesModal(activity_index)">&times;</button>
                            </div>
                            <div class="modal-body rule-modal">
                                <div class="form-row col-md-12 p-0" v-for="(styleItems, style_item_index) in activity.styles">
                                    <input type="hidden" :name="'activities['+activity_index+'][styleid][]'" :value="styleItems.styleid"/>
                                    <div class="form-inline col-md-4 m-0 p-0">
                                        <label class="col-md-3 pr-0" style="justify-content: left;">Type</label>
                                        <div class="col-md-9 p-0">
                                            <select :name="'activities['+activity_index+'][styles]['+style_item_index+'][fontfamily]'" class="form-control form-control-sm" v-model="styleItems.fontfamily" style="width:90%;">
                                                <option v-for="fontType in fontFamilies" :value="fontType.value">{{fontType.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-inline col-md-2 m-0 p-0">
                                        <label class="col-md-5 pr-0" style="justify-content: left;">Size</label>
                                        <div class="col-md-7 p-0">
                                            <select :name="'activities['+activity_index+'][styles]['+style_item_index+'][fontpt]'" class="form-control form-control-sm" v-model="styleItems.fontpt" style="width:90%;">
                                                <option v-for="fontType in fontSizes" :value="fontType.value">{{fontType.text}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-inline col-md-6 m-0 p-0">
                                        <div class="custom-control custom-checkbox custom-control-inline ml-2">
                                            <input type="checkbox" :name="'activities['+activity_index+'][styles]['+style_item_index+'][bold]'" v-model="styleItems.boldtext" :id="'activities['+activity_index+'][styles]['+style_item_index+'][bold]'" class="form-inline custom-control-input"/>
                                            <label class="custom-control-label" :for="'activities['+activity_index+'][styles]['+style_item_index+'][bold]'">Bold</label>
                                        </div>
                                        <div class="custom-control custom-checkbox custom-control-inline ml-2">
                                            <input type="checkbox" :name="'activities['+activity_index+'][styles]['+style_item_index+'][italic]'" v-model="styleItems.italic" :id="'activities['+activity_index+'][styles]['+style_item_index+'][italic]'" class="form-inline custom-control-input"/>
                                            <label class="custom-control-label" :for="'activities['+activity_index+'][styles]['+style_item_index+'][italic]'">Italic</label>
                                        </div>
                                        <div class="custom-control custom-checkbox custom-control-inline ml-2">
                                            <input type="checkbox" :name="'activities['+activity_index+'][styles]['+style_item_index+'][underline]'" v-model="styleItems.underline" :id="'activities['+activity_index+'][styles]['+style_item_index+'][underline]'" class="form-inline custom-control-input"/>
                                            <label class="custom-control-label" :for="'activities['+activity_index+'][styles]['+style_item_index+'][underline]'">Underline</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn btn-outline-primary btn-sm" @click="hideStylesModal(activity_index)">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-mask" v-show="activity.show_rules" transition="modal" aria-hidden="true" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content" style="width:800px;">
                            <div class="modal-header">

                                <span class="modal-title">Activity Rules</span>
                                <button type="button" class="close"  @click="hideRuleModal(activity_index)">&times;</button>
                            </div>
                            <div class="modal-body activity-rule-modal">
                                <div class="form-row col-md-12 p-0">
                                    <div class="form-inline col-md-12 m-0 p-0">
                                        <label class="col-md-3 pr-0" style="justify-content: left;">Rule Type</label>
                                        <div class="col-md-9 p-0">
                                            <div class="form-check-inline"  v-for="ruleType in ruleTypes">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" :name="'activities['+activity_index+'][arules][rule_type]'" :value="ruleType.value" :disabled="ruleType.value === 'activity' && activity_index===0" @change="getRuleType(activity_index,ruleType.value,$event)">{{ruleType.text}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_activity_rules" v-for="(activityRuleItems, activity_rule_item_index) in activity.arules">
                                        <div class="form-group col-md-6 pl-3">
                                            <label style="justify-content: left;">Preceding Activity</label>
                                            <div class="col-md-12 p-0">
                                                <select placeholder="Select Process" :name="'activities['+activity_index+'][arules]['+activity_rule_item_index+'][activity_id]'" v-model="activityRuleItems.activity_id" class="w-100 form-control form-control-sm" @change="getActivityType(activity_index,activity_rule_item_index,$event)">
                                                    <option value="" disabled hidden>Select Activity</option>
                                                    <option v-for="(value,key) in activityRuleItems.activities" :value="key">{{value}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label>Activity Value</label>
                                            <input type="text" :name="'activities['+activity_index+'][arules]['+activity_rule_item_index+'][activity_value]'" v-model="activityRuleItems.activity_value"  class="w-100 form-control form-control-sm" v-show="activityRuleItems.text">
                                            <select :name="'activities['+activity_index+'][arules]['+activity_rule_item_index+'][activity_value]'"  v-model="activityRuleItems.activity_value" class="w-100 form-control form-control-sm" v-show="activityRuleItems.dropdown">
                                                <option value="" disabled hidden>Select Value</option>
                                                <option v-for="(value,key) in activityRuleItems.dropdownitems" :value="value" >{{value}}</option>
                                            </select>

                                            <select :name="'activities['+activity_index+'][arules]['+activity_rule_item_index+'][activity_value]'"  v-model="activityRuleItems.activity_value" class="w-100 form-control form-control-sm" v-show="activityRuleItems.boolean">
                                                <option value="" disabled hidden>Please Select</option>
                                                <option :value="1">Yes</option>
                                                <option :value="0">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-sm btn-danger btn_rule_delete" style="margin-top: 1.4rem;" @click="deleteActivityRuleItem(activity_index, rule_item_index,activityRuleItems.rule_id)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_activity_rules">
                                    <button data-dismiss="modal" type="button" class="btn btn-sm btn-success" @click="createActivityRule(activity_index)" style="border-radius: 1.5rem;min-width:7rem;margin:0px auto;margin-bottom:16px;">Add Rule</button>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_step_rules" v-for="(ruleItems, rule_item_index) in activity.srules">
                                    <div class="form-group col-md-4 m-0 pl-3">
                                        <label>Activity Value</label>
                                        <input type="hidden" :name="'activities['+activity_index+'][srules]['+rule_item_index+'][rule_id]'" v-model="ruleItems.rule_id"  class="form-control form-control-sm">
                                        <input type="text" :name="'activities['+activity_index+'][srules]['+rule_item_index+'][rule_value]'" v-model="ruleItems.activity_value"  class="w-100 form-control form-control-sm" v-if="activity.type!=='dropdown' && activity.type!=='boolean'">
                                        <select :name="'activities['+activity_index+'][srules]['+rule_item_index+'][rule_value]'"  v-model="ruleItems.activity_value" class="form-control form-control-sm" v-if="activity.type==='dropdown'">
                                            <option value="" disabled hidden>Please Select</option>
                                            <option v-for="(dropdownItem, dropdown_item_index) in activity.dropdown_items" :value="dropdownItem">{{dropdownItem}}</option>
                                        </select>
                                        <select :name="'activities['+activity_index+'][srules]['+rule_item_index+'][rule_value]'"  v-model="ruleItems.activity_value" class="w-100 form-control form-control-sm" v-if="activity.type==='boolean'">
                                            <option value="" disabled hidden>Please Select</option>
                                            <option :value="1">Yes</option>
                                            <option :value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-7 m-0">
                                        <label>Step to enable</label>
                                        <select :name="'activities['+activity_index+'][srules]['+rule_item_index+'][rule_step]'"  v-model="ruleItems.step_id" class="w-100 form-control form-control-sm">
                                            <option value="" disabled hidden>Select Step</option>
                                            <option v-for="(value,key) in ruleItems.steps" :value="key" >{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-danger btn_rule_delete" style="margin-top: 1.4rem;" @click="deleteStepRuleItem(activity_index, rule_item_index)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_step_rules">
                                    <button data-dismiss="modal" type="button" class="btn btn-sm btn-success" @click="createStepRule(activity_index)" style="border-radius: 1.5rem;min-width:7rem;margin:0px auto;margin-bottom:16px;">Add Rule</button>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_process_rules" v-for="(ruleItems, rule_item_index) in activity.rules">
                                    <div class="form-group col-md-3 m-0 pl-3">
                                        <label>Activity Value</label>
                                        <input type="hidden" :name="'activities['+activity_index+'][rules]['+rule_item_index+'][rule_id]'" v-model="ruleItems.rule_id"  class="form-control form-control-sm">
                                        <input type="text" :name="'activities['+activity_index+'][rules]['+rule_item_index+'][rule_value]'" v-model="ruleItems.rule_value"  class="w-100 form-control form-control-sm" v-if="activity.type!=='dropdown' && activity.type!=='boolean'">
                                        <select :name="'activities['+activity_index+'][rules]['+rule_item_index+'][rule_value]'"  v-model="ruleItems.rule_value" class="w-100 form-control form-control-sm" v-if="activity.type==='dropdown'">
                                            <option value="" disabled hidden>Please Select</option>
                                            <option v-for="(dropdownItem, dropdown_item_index) in activity.dropdown_items" :value="dropdownItem">{{dropdownItem}}</option>
                                        </select>
                                        <select :name="'activities['+activity_index+'][rules]['+rule_item_index+'][rule_value]'"  v-model="ruleItems.rule_value" class="w-100 form-control form-control-sm" v-if="activity.type==='boolean'">
                                            <option value="" disabled hidden>Please Select</option>
                                            <option :value="1">Yes</option>
                                            <option :value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 m-0">
                                        <label>Process to start</label>
                                        <select :name="'activities['+activity_index+'][rules]['+rule_item_index+'][rule_process]'" placeholder="Select Process" v-model="ruleItems.rule_process" class="w-100 form-control form-control-sm" @change="getProcessSteps(activity_index,rule_item_index,$event)">
                                            <option value="" disabled hidden>Select Process</option>
                                            <option v-for="(value,key) in ruleItems.processs" :value="key">{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 m-0">
                                        <label>Start from process step</label>
                                        <select :name="'activities['+activity_index+'][rules]['+rule_item_index+'][rule_step]'"  v-model="ruleItems.rule_step" class="w-100 form-control form-control-sm">
                                            <option value="" disabled hidden>Select Step</option>
                                            <option v-for="(value,key) in ruleItems.stepss" :value="key" >{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-danger btn_rule_delete" style="margin-top: 1.4rem;" @click="deleteRuleItem(activity_index, rule_item_index)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_process_rules">
                                    <button data-dismiss="modal" type="button" class="btn btn-sm btn-success" @click="createRule(activity_index)" style="border-radius: 1.5rem;min-width:7rem;margin:0px auto;margin-bottom:16px;">Add Rule</button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn btn-outline-primary btn-sm" @click="hideRuleModal(activity_index)">Done</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-mask" v-show="activity.show_mirror" transition="modal" aria-hidden="true" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content" style="width:800px;">
                            <div class="modal-header">

                                <span class="modal-title">Activity Value to Mirror</span>
                                <button type="button" class="close"  @click="hideMirrorModal(activity_index)">&times;</button>
                            </div>
                            <div class="modal-body activity-mirror-modal">
                                <div class="form-row col-md-12 p-0">
                                    <div class="form-inline col-md-12 m-0 p-0">
                                        <label class="col-md-3 pr-0" style="justify-content: left;">Mirror Value Type</label>
                                        <div class="col-md-9 p-0">
                                            <div class="form-check-inline"  v-for="mirrorType in mirrorTypes">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" :name="'activities['+activity_index+'][mirrors][mirror_type]'" :value="mirrorType.value" @change="getMirrorType(activity_index,mirrorType.value,$event)">{{mirrorType.text}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_mirror_activity"  v-for="(mirrorItems, mirror_item_index) in activity.amirrors">
                                    <div class="form-group col-md-4 m-0">
                                        <input type="hidden" :name="'activities['+activity_index+'][amirrors]['+mirror_item_index+'][mirror_atype]'" class="form-control form-control-sm" v-model="activity.type" required/>
                                        <label>Process</label>
                                        <select :name="'activities['+activity_index+'][amirrors]['+mirror_item_index+'][mirror_process]'" placeholder="Select Process" v-model="mirrorItems.mirror_process" class="w-100 form-control form-control-sm" @change="getMirrorProcessSteps(activity_index,mirror_item_index,$event)">
                                            <option value="" disabled hidden>Select Process</option>
                                            <option v-for="(value,key) in mirrorItems.processs" :value="key">{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 m-0">
                                        <label>Step</label>
                                        <select :name="'activities['+activity_index+'][amirrors]['+mirror_item_index+'][mirror_step]'"  v-model="mirrorItems.mirror_step" class="w-100 form-control form-control-sm" @change="getMirrorStepActivities(activity_index,mirror_item_index,activity.type,$event)">
                                            <option value="" disabled hidden>Select Step</option>
                                            <option v-for="(value,key) in mirrorItems.stepss" :value="key" >{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 m-0">
                                        <label>Activity</label>
                                        <select :name="'activities['+activity_index+'][amirrors]['+mirror_item_index+'][mirror_activity]'"  v-model="mirrorItems.mirror_activity" class="w-100 form-control form-control-sm">
                                            <option value="" disabled hidden>Select Activity</option>
                                            <option v-for="(value,key) in mirrorItems.activitiess" :value="key" >{{value}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row col-md-12 p-0" v-show="activity.show_mirror_crm"  v-for="(mirrorItems, mirror_item_index) in activity.cmirrors">
                                    <input type="hidden" :name="'activities['+activity_index+'][cmirrors]['+mirror_item_index+'][mirror_atype]'" class="form-control form-control-sm" v-model="activity.type" required/>
                                    <div class="form-group col-md-4 m-0">
                                        <label>CRM</label>
                                        <select :name="'activities['+activity_index+'][cmirrors]['+mirror_item_index+'][mirror_process]'" placeholder="Select Process" v-model="mirrorItems.mirror_process" class="w-100 form-control form-control-sm" @change="getMirrorFormSections(activity_index,mirror_item_index,activity.type,$event)">
                                            <option value="" disabled hidden>Select Process</option>
                                            <option v-for="(value,key) in mirrorItems.processs" :value="key">{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 m-0">
                                        <label>Section</label>
                                        <select :name="'activities['+activity_index+'][cmirrors]['+mirror_item_index+'][mirror_step]'"  v-model="mirrorItems.mirror_step" class="w-100 form-control form-control-sm" @change="getMirrorSectionInputs(activity_index,mirror_item_index,activity.type,$event)">
                                            <option value="" disabled hidden>Select Step</option>
                                            <option v-for="(value,key) in mirrorItems.stepss" :value="key" >{{value}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 m-0">
                                        <label>Input</label>
                                        <select :name="'activities['+activity_index+'][cmirrors]['+mirror_item_index+'][mirror_activity]'"  v-model="mirrorItems.mirror_activity" class="w-100 form-control form-control-sm">
                                            <option value="" disabled hidden>Select Activity</option>
                                            <option v-for="(value,key) in mirrorItems.activitiess" :value="key" >{{value}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row col-md-12 p-0" v-if="activity.type === 'text'" v-show="activity.show_mirror_default"  v-for="(mirrorItems, mirror_item_index) in activity.dmirrors">
                                    <input type="hidden" :name="'activities['+activity_index+'][dmirrors]['+mirror_item_index+'][mirror_atype]'" class="form-control form-control-sm" v-model="activity.type" required/>
                                    <div class="form-group col-md-12 m-0">
                                        <label>Default</label>
                                        <select :name="'activities['+activity_index+'][dmirrors]['+mirror_item_index+'][mirror_value]'" placeholder="Select Default Value" v-model="mirrorItems.mirror_value" class="w-100 form-control form-control-sm" @change="getMirrorFormSections(activity_index,mirror_item_index,$event)">
                                            <option value="" disabled hidden>Select Default Value</option>
                                            <option v-for="(value,key) in mirrorItems.processs" :value="key">{{value}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" type="button" class="btn btn-outline-primary btn-sm" @click="hideMirrorModal(activity_index)">Done</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row" v-if="activity.type==='amount'">
                    <div class="form-inline col-md-6">
                        <label>Currency:&nbsp;&nbsp;</label>

                        <div class="form-inline">
                            <select :name="'activities['+activity_index+'][currency_type]'" class="form-control form-control-sm" v-model="activity.currencies">
                                <option value="0">None</option>
                                <option v-for="(cur, cur_index) in currencies" :value="cur_index">{{cur}}</option>
                            </select>
                        </div>

                    </div>
                </div>

                <p class="text-center">
                    <button type="button" title="Move activity up" class="btn btn-outline-secondary btn-sm" :class="[activity_index===0 ? 'disabled' : '']" @click="moveActivity(true, activity_index)"><i class="fa fa-fw" :class="[activity_index===0 ? 'fa-minus' : 'fa-arrow-up']"></i></button>
                    <button type="button" title="Move activity down" class="btn btn-outline-secondary btn-sm" :class="[activity_index===activities.length-1 ? 'disabled' : '']" @click="moveActivity(false, activity_index)"><i class="fa fa-fw" :class="[activity_index===activities.length-1 ? 'fa-minus' : 'fa-arrow-down']"></i></button>
                    <button type="button" title="Activity rules"  @click="showRuleModal(activity_index)" class="btn btn-outline-primary btn-sm" :disabled="activity.report == 1"><i class="fa fa-fw fa-gavel"></i></button>
                    <button type="button" title="Mirror another activity's value" v-if="activity.type!=='heading' && activity.type!=='subheading' && activity.type!=='content' && activity.type!=='notification' && activity.type!=='document_email' && activity.type!=='template_email' && activity.type!=='multiple_attachment' && activity.type!=='document'" class="btn btn-outline-primary btn-sm" @click="showMirrorModal(activity_index)" :disabled="activity.report == 1"><i class="fa fa-fw fa-map"></i></button>
                    <button type="button" title="Mirror another activity's value" v-if="activity.type==='heading' || activity.type==='subheading' || activity.type==='content' || activity.type==='notification' || activity.type==='document_email' || activity.type==='template_email' || activity.type==='multiple_attachment' || activity.type==='document'" class="btn btn-outline-primary btn-sm" @click="showMirrorModal(activity_index)" :disabled="true"><i class="fa fa-fw fa-map"></i></button>
                    <!--<button type="button" title="Activity rules"  @click="showActivityRuleModal(activity_index)" class="btn btn-outline-primary btn-sm" :disabled="activity.report == 1 || activity_index===0"><i class="fa fa-fw fa-handshake"></i></button>-->
                    <button type="button" title="Delete activity" class="btn btn-outline-danger btn-sm" @click="deleteActivity(activity_index)" :disabled="activity.report == 1"><i class="fa fa-fw fa-trash"></i></button>
                </p>


            </li>
            <li class="text-center list-group-item">
                <button type="button" class="btn btn-outline-success btn-sm" @click="createHeading()"><i class="fa fa-plus"></i> Heading</button>
                <button type="button" class="btn btn-outline-success btn-sm" @click="createSubheading()"><i class="fa fa-plus"></i> Sub-heading</button>
                <button type="button" class="btn btn-outline-success btn-sm" @click="createActivity()"><i class="fa fa-plus"></i> Add Type</button>
            </li>
        </ul>
</template>

<script>


    export default {
        props: {
            blackFields:{
                type: Array,
            },
            blackActivities: {
                type: Array,
                default: () => [
                    {
                        id:0,
                        // mappedFields: this.blackFields,
                        color:'',
                        type: 'text',
                        myToolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                        kpi: true,
                        comment: false,
                        value: false,
                        procedure: false,
                        client: false,
                        weight: 0,
                        dropdown_item: '',
                        dropdown_items: [],
                        is_tooltip_shown: false,
                        is_text_content_shown: false,
                        is_file_items_shown: false,
                        is_dropdown_items_shown: false,
                        is_grouping_items_shown: false,
                        is_default_value_shown: false,
                        is_client_bucket:false,
                        is_currency_items_shown: true,
                        currencies: 0,
                        currency_type: 0,
                        currency_format_: null,
                        threshold: {
                            time: 7,
                            type: 'days'
                        },
                        user: 0,
                        files: [],
                        file:'',
                        rules: [],
                        rule:'',
                        amirrors: [],
                        cmirrors: [],
                        dmirrors: [],
                        mirror:'',
                        uploadPercentage: 0,
                        showPercentage: false,
                        report: false,
                        show_styles: false,
                        styles:[],
                        srules:[],
                        arules:[],
                        show_rules: false,
                        show_mirror: false,
                        show_process_rules: false,
                        show_step_rules: false,
                        show_activity_rules: false,
                        show_mirror_crm: false,
                        show_mirror_activity: false,
                        show_mirror_default: false
                    }
                ]
            }
        },
        data() {
            return {
                id: 0,
                mappedFields: this.blackFields,
                color: '',
                files: [],
                file: '',
                amirrors:[],
                cmirrors:[],
                rules: [],
                rule:'',
                show_rules: false,
                uploadPercentage: 0,
                showPercentage: false,
                activities: this.blackActivities,
                users: [],
                filesSelected:[],
                //activity type hook
                fontFamilies: [
                    {text: 'Andale Mono',value: 'Andale Mono'},
                    {text: 'Arial',value: 'Arial'},
                    {text: 'Book Antique',value: 'Book Antique'},
                    {text: 'Comic Sans MS',value: 'Comic Sans MS'},
                    {text: 'Courier New',value: 'Courier New'},
                    {text: 'Georgia',value: 'Georgia'},
                    {text: 'Helvetica',value: 'Helvetica'},
                    {text: 'Impact',value: 'Impact'},
                    {text: 'Tahoma',value: 'Tahoma'},
                    {text: 'Terminal',value: 'Terminal'},
                    {text: 'Times New Roman',value: 'Times New Roman'},
                    {text: 'Verdana',value: 'Verdana'},
                ],
                fontSizes: [
                    {text: '8pt',value: '8pt'},
                    {text: '10pt',value: '10pt'},
                    {text: '11pt',value: '11pt'},
                    {text: '12pt',value: '12pt'},
                    {text: '14pt',value: '14pt'},
                    {text: '18pt',value: '18pt'},
                    {text: '24pt',value: '24pt'},
                    {text: '36pt',value: '36pt'},
                ],
                alignmentTypes: [
                    {text: 'left', value: 'left'},
                    {text: 'center', value: 'center'},
                    {text: 'right', value: 'right'},
                ],
                actionableTypes: [
                    /*{text: 'Font', value: 'font'},*/
                    {text: 'Text', value: 'text'},
                    {text: 'Textarea', value: 'textarea'},
                    {text: 'Percentage', value: 'percentage'},
                    {text: 'Integer', value: 'integer'},
                    {text: 'Letter Email', value: 'template_email'},
                    {text: 'Document Email', value: 'document_email'},
                    {text: 'Document Upload', value: 'document'},
                    {text: 'Dropdown', value: 'dropdown'},
                    {text: 'Date', value: 'date'},
                    {text: 'Y/N', value: 'boolean'},
                    {text: 'Notification', value: 'notification'},
                    {text: 'Youtube Video', value: 'videoyoutube'},
                    {text: 'Video', value: 'videoupload'},
                    {text: 'Image', value: 'imageupload'},
                    {text: 'Multiple Attachment', value: 'multiple_attachment'},
                    {text: 'Amount', value: 'amount'},
                    {text: 'Heading', value: 'heading'},
                    {text: 'Sub-heading', value: 'subheading'},
                    {text: 'Content', value: 'content'},
                ],
                thresholdTypes: [
                    {text: 'Seconds', value: 'seconds'},
                    {text: 'Minutes', value: 'minutes'},
                    {text: 'Hours', value: 'hours'},
                    {text: 'Days', value: 'days'}
                ],
                positionTypes: [
                    {text: 'None', value: '0'},
                    {text: 'Top', value: '0'},
                    {text: 'Left - Top', value: '1'},
                    {text: 'Left - Bottom', value: '3'},
                    {text: 'Right - Top', value: '2'},
                    {text: 'Right - Bottom', value: '4'},
                    {text: 'Bottom', value: '5'}
                ],
                levelTypes: [
                    {text: '0', value: '0'},
                    {text: '1', value: '1'},
                    {text: '2', value: '2'}
                ],
                groupingTypes: [
                    {text: '0', value: '0'},
                    {text: '1', value: '1'},
                    {text: '2', value: '2'},
                    {text: '3', value: '3'},
                    {text: '4', value: '4'},
                    {text: '5', value: '5'},
                    {text: '6', value: '6'},
                    {text: '7', value: '7'},
                    {text: '8', value: '8'},
                    {text: '9', value: '9'},
                    {text: '10', value: '10'},
                    {text: '11', value: '11'},
                    {text: '12', value: '12'},
                    {text: '13', value: '13'},
                    {text: '14', value: '14'},
                    {text: '15', value: '15'},
                    {text: '16', value: '16'},
                    {text: '17', value: '17'},
                    {text: '18', value: '18'},
                    {text: '19', value: '19'},
                    {text: '20', value: '20'},
                    {text: '21', value: '21'},
                    {text: '22', value: '22'},
                    {text: '23', value: '23'},
                    {text: '24', value: '24'},
                    {text: '25', value: '25'},
                    {text: '26', value: '26'},
                    {text: '27', value: '27'},
                    {text: '28', value: '28'},
                    {text: '29', value: '29'},
                    {text: '30', value: '30'},
                    {text: '31', value: '31'},
                    {text: '32', value: '32'},
                    {text: '33', value: '33'},
                    {text: '34', value: '34'},
                    {text: '35', value: '35'},
                    {text: '36', value: '36'},
                    {text: '37', value: '37'},
                    {text: '38', value: '38'},
                    {text: '39', value: '39'},
                    {text: '40', value: '40'},
                    {text: '41', value: '41'},
                    {text: '42', value: '42'},
                    {text: '43', value: '43'},
                    {text: '44', value: '44'},
                    {text: '45', value: '45'},
                    {text: '46', value: '46'},
                    {text: '47', value: '47'},
                    {text: '48', value: '48'},
                    {text: '49', value: '49'},
                    {text: '50', value: '50'},
                    {text: '51', value: '51'},
                    {text: '52', value: '52'},
                    {text: '53', value: '53'},
                    {text: '54', value: '54'},
                    {text: '55', value: '55'},
                    {text: '56', value: '56'},
                    {text: '57', value: '57'},
                    {text: '58', value: '58'},
                    {text: '59', value: '59'},
                    {text: '60', value: '60'},
                    {text: '61', value: '61'},
                    {text: '62', value: '62'},
                    {text: '63', value: '63'},
                    {text: '64', value: '64'},
                    {text: '65', value: '65'},
                    {text: '66', value: '66'},
                    {text: '67', value: '67'},
                    {text: '68', value: '68'},
                    {text: '69', value: '69'},
                    {text: '70', value: '70'},
                    {text: '71', value: '71'},
                    {text: '72', value: '72'},
                    {text: '73', value: '73'},
                    {text: '74', value: '74'},
                    {text: '75', value: '75'},
                    {text: '76', value: '76'},
                    {text: '77', value: '77'},
                    {text: '78', value: '78'},
                    {text: '79', value: '79'},
                    {text: '80', value: '80'},
                    {text: '81', value: '81'},
                    {text: '82', value: '82'},
                    {text: '83', value: '83'},
                    {text: '84', value: '84'},
                    {text: '85', value: '85'},
                    {text: '86', value: '86'},
                    {text: '87', value: '87'},
                    {text: '88', value: '88'},
                    {text: '89', value: '89'},
                    {text: '90', value: '90'},
                    {text: '91', value: '91'},
                    {text: '92', value: '92'},
                    {text: '93', value: '93'},
                    {text: '94', value: '94'},
                    {text: '95', value: '95'},
                    {text: '96', value: '96'},
                    {text: '97', value: '97'},
                    {text: '98', value: '98'},
                    {text: '99', value: '99'},
                    {text: '100', value: '100'},
                    {text: '101', value: '101'},
                    {text: '102', value: '102'},
                    {text: '103', value: '103'},
                    {text: '104', value: '104'},
                    {text: '105', value: '105'},
                    {text: '106', value: '106'},
                    {text: '107', value: '107'},
                    {text: '108', value: '108'},
                    {text: '109', value: '109'},
                    {text: '110', value: '110'},
                    {text: '111', value: '111'},
                    {text: '112', value: '112'},
                    {text: '113', value: '113'},
                    {text: '114', value: '114'},
                    {text: '115', value: '115'},
                    {text: '116', value: '116'},
                    {text: '117', value: '117'},
                    {text: '118', value: '118'},
                    {text: '119', value: '119'},
                    {text: '120', value: '120'},
                    {text: '121', value: '121'},
                    {text: '122', value: '122'},
                    {text: '123', value: '123'},
                    {text: '124', value: '124'},
                    {text: '125', value: '125'},
                    {text: '126', value: '126'},
                    {text: '127', value: '127'},
                    {text: '128', value: '128'},
                    {text: '129', value: '129'},
                    {text: '130', value: '130'},
                    {text: '131', value: '131'},
                    {text: '132', value: '132'},
                    {text: '133', value: '133'},
                    {text: '134', value: '134'},
                    {text: '135', value: '135'},
                    {text: '136', value: '136'},
                    {text: '137', value: '137'},
                    {text: '138', value: '138'},
                    {text: '139', value: '139'},
                    {text: '140', value: '140'},
                    {text: '141', value: '141'},
                    {text: '142', value: '142'},
                    {text: '143', value: '143'},
                    {text: '144', value: '144'},
                    {text: '145', value: '145'},
                    {text: '146', value: '146'},
                    {text: '147', value: '147'},
                    {text: '148', value: '148'},
                    {text: '149', value: '149'},
                    {text: '150', value: '150'},
                    {text: '151', value: '151'},
                    {text: '152', value: '152'},
                    {text: '153', value: '153'},
                    {text: '154', value: '154'},
                    {text: '155', value: '155'},
                    {text: '156', value: '156'},
                    {text: '157', value: '157'},
                    {text: '158', value: '158'},
                    {text: '159', value: '159'},
                    {text: '160', value: '160'},
                    {text: '161', value: '161'},
                    {text: '162', value: '162'},
                    {text: '163', value: '163'},
                    {text: '164', value: '164'},
                    {text: '165', value: '165'},
                    {text: '166', value: '166'},
                    {text: '167', value: '167'},
                    {text: '168', value: '168'},
                    {text: '169', value: '169'},
                    {text: '170', value: '170'},
                    {text: '171', value: '171'},
                    {text: '172', value: '172'},
                    {text: '173', value: '173'},
                    {text: '174', value: '174'},
                    {text: '175', value: '175'},
                    {text: '176', value: '176'},
                    {text: '177', value: '177'},
                    {text: '178', value: '178'},
                    {text: '179', value: '179'},
                    {text: '180', value: '180'},
                    {text: '181', value: '181'},
                    {text: '182', value: '182'},
                    {text: '183', value: '183'},
                    {text: '184', value: '184'},
                    {text: '185', value: '185'},
                    {text: '186', value: '186'},
                    {text: '187', value: '187'},
                    {text: '188', value: '188'},
                    {text: '189', value: '189'},
                    {text: '190', value: '190'},
                    {text: '191', value: '191'},
                    {text: '192', value: '192'},
                    {text: '193', value: '193'},
                    {text: '194', value: '194'},
                    {text: '195', value: '195'},
                    {text: '196', value: '196'},
                    {text: '197', value: '197'},
                    {text: '198', value: '198'},
                    {text: '199', value: '199'},
                    {text: '200', value: '200'},
                ],
                ruleTypes:[
                    {text:'Start a different Application',value:'process'},
                    {text:'Enable / Show Step',value:'step'},
                    {text:'Enable / Show Activity',value:'activity'}
                ],
                mirrorTypes:[
                    {text:'Default',value:'default'},
                    {text:'CRM',value:'crm'},
                    {text:'Activity',value:'activity'}
                ],
                arules:[],
                srules:[],
                is_grouping_items_shown: false,
                is_default_value_shown: false,
                is_tooltip_shown: false,
                is_text_content_shown: false,
                is_client_bucket:false,
                show_styles: false,
                show_mirror: false,
                styles:[],
                myToolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            }
        },
        components: {
            'tinymce-editor': Editor // <- Important part
        },
        methods: {

            GetCurrencies: function(){
                axios.post('/getcurrencies')
                    .then(response => {
                        this.currencies = response.data;
                    })
                    .catch(error => {
                        // todo handle error
                    });
            },
            createActivity(index) {
                this.activities.push({
                    color: '',
                    name: '',
                    type: 'text',
                    kpi: true,
                    comment: false,
                    value: false,
                    procedure: false,
                    client: false,
                    weight: 0,
                    dropdown_item: '',
                    dropdown_items: [],
                    is_file_items_shown: true,
                    is_dropdown_items_shown: false,
                    is_grouping_items_shown: false,
                    is_default_value_shown: false,
                    is_tooltip_shown: false,
                    is_text_content_shown: false,
                    is_client_bucket: false,
                    threshold: {
                        time: 7,
                        type: 'days'
                    },
                    user: 0,
                    currency: 0,
                    default_value: '',
                    level: 0,
                    position: 0,
                    files:[],
                    file:'',
                    rules: [],
                    rule:'',
                    show_rules: false,
                    uploadPercentage: 0,
                    show_styles: false,
                    show_mirror: false,
                    styles:[
                        {'boldtext':null}
                    ]
                });
            },
            createHeading(index) {
                this.activities.push({
                    color: '',
                    name: '',
                    type: 'heading',
                    kpi: true,
                    comment: false,
                    value: false,
                    procedure: false,
                    client: false,
                    weight: 0,
                    dropdown_item: '',
                    dropdown_items: [],
                    is_file_items_shown: true,
                    is_dropdown_items_shown: false,
                    is_grouping_items_shown: false,
                    is_default_value_shown: false,
                    is_tooltip_shown: false,
                    is_text_content_shown: false,
                    is_client_bucket: false,
                    threshold: {
                        time: 7,
                        type: 'days'
                    },
                    user: 0,
                    currency: 0,
                    default_value: '',
                    level: 0,
                    position: 0,
                    files:[],
                    file:'',
                    rules: [],
                    rule:'',
                    show_rules: false,
                    show_mirror: false,
                    uploadPercentage: 0,
                    show_styles: false,
                    styles:[]
                });
            },
            createSubheading(index) {
                this.activities.push({
                    color: '',
                    name: '',
                    type: 'subheading',
                    kpi: true,
                    comment: false,
                    value: false,
                    procedure: false,
                    client: false,
                    weight: 0,
                    dropdown_item: '',
                    dropdown_items: [],
                    is_file_items_shown: true,
                    is_dropdown_items_shown: false,
                    is_grouping_items_shown: false,
                    is_default_value_shown: false,
                    is_tooltip_shown: false,
                    is_client_bucket: false,
                    threshold: {
                        time: 7,
                        type: 'days'
                    },
                    user: 0,
                    currency: 0,
                    default_value: '',
                    level: 0,
                    position: 0,
                    files:[],
                    file:'',
                    rules: [],
                    rule:'',
                    show_rules: false,
                    uploadPercentage: 0,
                    show_styles: false,
                    show_mirror: false,
                    styles:[]
                });
            },
            deleteActivity(index) {
                if(confirm("Do you really want to delete this activity?")) {
                    this.activities.splice(index, 1);
                }
            },
            moveActivity(direction, index) {
                if (this.activities.length !== 1 && !(index === 0 && direction) && !(index === (this.activities.length - 1) && !direction)) {
                    if (direction) {
                        const b = this.activities[index];
                        Vue.set(this.activities, index, this.activities[index - 1]);
                        Vue.set(this.activities, index - 1, b);
                    } else {
                        const b = this.activities[index];
                        Vue.set(this.activities, index, this.activities[index + 1]);
                        Vue.set(this.activities, index + 1, b);
                    }
                }
            },
            createDropdownItem(index) {
                if (!this.activities[index].dropdown_items) {
                    this.activities[index].dropdown_items = [];
                }

                this.activities[index].dropdown_items.push(
                    this.activities[index].dropdown_item
                );

                this.activities[index].dropdown_item = '';
            },
            deleteDropdownItem(index, dropdown_item_index) {
                this.activities[index].dropdown_items.splice(dropdown_item_index, 1);
            },
            toggleDropdownItemShown(index) {
                this.activities[index].is_dropdown_items_shown = !this.activities[index].is_dropdown_items_shown;
            },
            checkEmail(index) {
                this.activities[index].is_default_value_shown = !this.activities[index].is_default_value_shown;
            },
            disen(event) {
                console.log(event);
                if(event === '1'){
                    this.is_grouping_items_shown = true;
                } else {
                    this.is_grouping_items_shown = false;
                }
            },
            toggleTooltipShown(index) {
                this.activities[index].is_tooltip_shown = !this.activities[index].is_tooltip_shown;
            },
            toggleDefaultValueShown(index) {
                this.activities[index].is_default_value_shown = !this.activities[index].is_default_value_shown;
            },
            toggleContentTextShown(index) {
                this.activities[index].is_text_content_shown = !this.activities[index].is_text_content_shown;
            },
            remove (index) {
                this.files.splice(index, 1)
            },
            videoChanged (index) {
                $('.submitbtn').attr('disabled',true);
                $('.submitbtn').html('<i class="fas fa-spinner fa-pulse"></i>&nbsp;Please Wait...');
                this.activities[index].showPercentage = !this.activities[index].showPercentage;
                let self = this;
                let formData = new FormData();

                formData.append('videoFile', this.activities[index].files[0]);

                axios.post('/video/upload', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.activities[index].uploadPercentage = Math.round( ( progressEvent.loaded * 100) / progressEvent.total  );
                            console.log(Math.round( ( progressEvent.loaded * 100) / progressEvent.total  ));
                        }.bind(this)
                    })
                    .then(function (response) {

                        if(!response.data){
                            alert('File not uploaded.');
                            $('.submitbtn').attr('disabled',false);
                            $('.submitbtn').html('<i class="fa fa-save"></i> Save');
                        }else{
                            if(self.activities[index].old_value === '' ){
                                self.activities[index].old_value = response.data.filename;
                            }
                            self.activities[index].default_value = response.data.filename;

                            $('.submitbtn').attr('disabled',false);
                            $('.submitbtn').html('<i class="fa fa-save"></i> Save');
                            self.activities[index].showPercentage = !self.activities[index].showPercentage;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            imageChanged (index) {
                $('.submitbtn').attr('disabled',true);
                $('.submitbtn').html('<i class="fas fa-spinner fa-pulse"></i>&nbsp;Please Wait...');
                this.activities[index].showPercentage = !this.activities[index].showPercentage;
                let self = this;
                let formData = new FormData();



                formData.append('imageFile', this.activities[index].files[0]);

                axios.post('/image/upload', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            this.activities[index].uploadPercentage = Math.round( ( progressEvent.loaded * 100) / progressEvent.total  );
                            console.log(Math.round( ( progressEvent.loaded * 100) / progressEvent.total  ));
                        }.bind(this)
                    })
                    .then(function (response) {

                        if(!response.data){
                            alert('File not uploaded.');
                            $('.submitbtn').attr('disabled',false);
                            $('.submitbtn').html('<i class="fa fa-save"></i> Save');
                        }else{
                            if(self.activities[index].old_value === '' ){
                                self.activities[index].old_value = response.data.filename;
                            }
                            self.activities[index].default_value = response.data.filename;

                            $('.submitbtn').attr('disabled',false);
                            $('.submitbtn').html('<i class="fa fa-save"></i> Save');
                            self.activities[index].showPercentage = !self.activities[index].showPercentage;
                        }

                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            showStylesModal(index) {
                this.activities[index].show_styles = !this.activities[index].show_styles;
            },
            hideStylesModal(index) {
                this.activities[index].show_styles = !this.activities[index].show_styles;
            },
            createRule(index) {
                console.log(this.activities[index].rules);
                let i = (this.activities[index].rules.length);

                if (!this.activities[index].rules) {
                    //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
                    []
                }

                this.activities[index].rules.push(
                    {rule_value:null,rule_process:null,rule_step:null,processs:null,stepss:null}
                );

                axios.get('/getprocesses2')
                    .then(response => {
                        this.activities[index].rules[i].processs = response.data;
                        this.activities[index].rules[i].rule_process = '';
                        this.activities[index].rules[i].rule_step = '';
                    })
                    .catch(error => {
                        // todo handle error
                    });

                console.log(this.activities[index].rules);
            },
            deleteRuleItem(index, rule_item_index) {
                this.activities[index].rules.splice(rule_item_index, 1);
            },
            deleteStepRuleItem(index, rule_item_index) {
                this.activities[index].srules.splice(rule_item_index, 1);
            },
            getProcessSteps(index,rule_item_index,event){

                let process_id = event.target.value;

                axios.get('/getsteps/?process_id='+process_id)
                    .then(response => {


                        this.activities[index].rules[rule_item_index].stepss = response.data;
                        this.activities[index].rules[rule_item_index].rule_process = process_id;
                        this.activities[index].rules[rule_item_index].stepss.$forceUpdate();
                        this.activities[index].rules[rule_item_index].rule_process.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });
            },
            getMirrorProcessSteps(index,mirror_item_index,event){

                let process_id = event.target.value;

                axios.get('/getsteps/?process_id='+process_id)
                    .then(response => {


                        this.activities[index].amirrors[mirror_item_index].stepss = response.data;
                        this.activities[index].amirrors[mirror_item_index].stepss.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });
            },
            getMirrorFormSections(index,mirror_item_index,activity_type,event){
                console.log(activity_type);
                let process_id = event.target.value;

                axios.get('/getsections/?form_id='+process_id)
                    .then(response => {


                        this.activities[index].cmirrors[mirror_item_index].stepss = response.data;
                        this.activities[index].cmirrors[mirror_item_index].stepss.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });
            },
            getMirrorStepActivities(index,mirror_item_index,activity_type,event){

                let step_id = event.target.value;

                axios.get('/getstepactivities/?step_id='+step_id+'&atype='+activity_type)
                    .then(response => {

                    if(response.data.length === 0){
                        alert('There are no activities matching the current activity type');
                    }
                        this.activities[index].amirrors[mirror_item_index].activitiess = response.data;
                        this.activities[index].amirrors[mirror_item_index].activitiess.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });
            },
            getMirrorSectionInputs(index,mirror_item_index,activity_type,event){

                let step_id = event.target.value;


                axios.get('/getsectioninputs/?form_section_id='+step_id+'&aatype='+activity_type)
                    .then(response => {

                        if(response.data.length === 0){
                            alert('There are no form inputs matching the current input type');
                        }

                        this.activities[index].cmirrors[mirror_item_index].activitiess = response.data;
                        this.activities[index].cmirrors[mirror_item_index].activitiess.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });
            },
            showRuleModal(index) {
                this.activities[index].show_rules = !this.activities[index].show_rules;
            },
            hideRuleModal(index) {
                this.activities[index].show_rules = !this.activities[index].show_rules;
            },
            showMirrorModal(index) {
                this.activities[index].show_mirror = !this.activities[index].show_mirror;
            },
            hideMirrorModal(index) {
                this.activities[index].show_mirror = !this.activities[index].show_mirror;
            },
            /*showActivityRuleModal(index) {
                this.activities[index].show_rules = !this.activities[index].show_rules;
            },
            hideActivityRuleModal(index) {
                this.activities[index].show_rules = !this.activities[index].show_rules;
            },*/
            createStepRule(index) {

                let i = (this.activities[index].srules.length);

                if (!this.activities[index].srules) {
                    //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
                    []
                }

                this.activities[index].srules.push(
                    {steps:null,step_id:null,activity_value:null,boolean:false,dropdown:false,dropdownitems:null,text:false}
                );

                this.activities[index].srules[i].step_id = '';
                this.activities[index].srules[i].boolean = false;
                this.activities[index].srules[i].dropdown = false;
                this.activities[index].srules[i].dropdownitems = '';
                this.activities[index].srules[i].text = false;
                this.activities[index].srules[i].activity_value = '';

                let step_id = this.activities[index].step_id;
                let activity_id = this.activities[index].id;

                axios.get('/getremainingsteps/?step_id='+step_id+'&activity_id='+activity_id)
                    .then(response => {

                        console.log(response.data);
                        this.activities[index].srules[i].steps = response.data;
                        this.activities[index].srules[i].steps.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });

            },
            createActivityRule(index) {
                console.log(this.activities[index].arules);
                let i = (this.activities[index].arules.length);

                if (!this.activities[index].arules) {
                    //this.activities[index].rules = {rule_value:'',rule_process:'',rule_step:'',processs:'',stepss:''};
                    []
                }

                this.activities[index].arules.push(
                    {activities:null,activity_id:null,activity_value:null,boolean:false,dropdown:false,dropdownitems:null,text:false}
                );

                this.activities[index].arules[i].activity_id = '';
                this.activities[index].arules[i].boolean = false;
                this.activities[index].arules[i].dropdown = false;
                this.activities[index].arules[i].dropdownitems = '';
                this.activities[index].arules[i].text = false;
                this.activities[index].arules[i].activity_value = '';

                let step_id = this.activities[index].step_id;
                let activity_id = this.activities[index].id;

                axios.get('/getactivities/?step_id='+step_id+'&activity_id='+activity_id)
                    .then(response => {

                        console.log(response.data.activities);
                        this.activities[index].arules[i].activities = response.data.activities;
                        this.activities[index].arules[i].activities.$forceUpdate();
                    })
                    .catch(error => {
                        // todo handle error
                    });

                console.log(this.activities);
            },
            deleteActivityRuleItem(index, rule_item_index) {
                this.activities[index].arules.splice(rule_item_index, 1);
            },
            listofActivities(){
                console.log(activities);
            },
            getActivityType(index,activity_rule_item_index,event){

                let activity_id = event.target.value;

                axios.get('/getactivitytype/?activity_id='+activity_id)
                    .then(response => {
                        if(response.data.activity_type === 'boolean' || response.data.activity_type === 'notification' || response.data.activity_type === 'multiple_attachment' || response.data.activity_type === 'date' || response.data.activity_type === 'document' || response.data.activity_type === 'template_email' || response.data.activity_type === 'document_email'){
                            console.log(activity_rule_item_index);
                            this.activities[index].arules[activity_rule_item_index].boolean = true;
                            this.activities[index].arules[activity_rule_item_index].dropdown = false;
                            this.activities[index].arules[activity_rule_item_index].text = false;
                            this.activities[index].arules[activity_rule_item_index].boolean.$forceUpdate();
                            this.activities[index].arules[activity_rule_item_index].dropdown.$forceUpdate();
                            this.activities[index].arules[activity_rule_item_index].text.$forceUpdate();
                        }

                        if(response.data.activity_type === 'dropdown'){
                            console.log(activity_rule_item_index);
                            this.activities[index].arules[activity_rule_item_index].boolean = false;
                            this.activities[index].arules[activity_rule_item_index].dropdown = true;
                            this.activities[index].arules[activity_rule_item_index].text = false;

                            axios.get('/getdropdownitems/?activity_id='+activity_id)
                                .then(response => {
                                        this.activities[index].arules[activity_rule_item_index].dropdownitems = response.data.dropdownitems;
                                        this.activities[index].arules[activity_rule_item_index].dropdownitems.$forceUpdate();
                                })
                                .catch(error => {
                                    // todo handle error
                                });
                            this.activities[index].arules[activity_rule_item_index].boolean.$forceUpdate();
                            this.activities[index].arules[activity_rule_item_index].dropdown.$forceUpdate();
                            this.activities[index].arules[activity_rule_item_index].text.$forceUpdate();
                        }

                        if(response.data.activity_type !== 'boolean' && response.data.activity_type !== 'dropdown' && response.data.activity_type !== 'notification' && response.data.activity_type !== 'multiple_attachment' && response.data.activity_type !== 'date' && response.data.activity_type !== 'document' && response.data.activity_type !== 'template_email' && response.data.activity_type !== 'document_email'){
                            console.log(activity_rule_item_index);
                            this.activities[index].arules[activity_rule_item_index].boolean = false;
                            this.activities[index].arules[activity_rule_item_index].dropdown = false;
                            this.activities[index].arules[activity_rule_item_index].text = true;
                            this.activities[index].arules[activity_rule_item_index].boolean.$forceUpdate();
                            this.activities[index].arules[activity_rule_item_index].dropdown.$forceUpdate();
                            this.activities[index].arules[activity_rule_item_index].text.$forceUpdate();
                        }

                    })
                    .catch(error => {
                        // todo handle error
                    });


            },
            getRuleType(index,rule_type,event){
                let type = event.target.value;

                if(type === 'process'){
                    this.activities[index].show_process_rules = true;
                    this.activities[index].show_step_rules = false;
                    this.activities[index].show_activity_rules = false;
                }

                if(type === 'step'){
                    this.activities[index].show_process_rules = false;
                    this.activities[index].show_step_rules = true;
                    this.activities[index].show_activity_rules = false;
                }

                if(type === 'activity'){
                    this.activities[index].show_process_rules = false;
                    this.activities[index].show_step_rules = false;
                    this.activities[index].show_activity_rules = true;
                }
            },
            getMirrorType(index,mirror_type,event){
                let type = event.target.value;

                if(type === 'crm'){
                    this.activities[index].show_mirror_default = false;
                    this.activities[index].show_mirror_crm = true;
                    this.activities[index].show_mirror_activity = false;
                }

                if(type === 'activity'){
                    this.activities[index].show_mirror_default = false;
                    this.activities[index].show_mirror_crm = false;
                    this.activities[index].show_mirror_activity = true;
                }

                if(type === 'default'){
                    this.activities[index].show_mirror_default = true;
                    this.activities[index].show_mirror_crm = false;
                    this.activities[index].show_mirror_activity = false;
                }
            }
        },
        created() {
            this.GetCurrencies();
        }
    }


</script>