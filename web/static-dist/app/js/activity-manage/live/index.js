webpackJsonp(["app/js/activity-manage/live/index"],{"697c4beb0e34534c4c01":function(e,t,i){"use strict";var a=i("6fc36d688eaf991f2202");new(function(e){return e&&e.__esModule?e:{default:e}}(a).default)},"6fc36d688eaf991f2202":function(e,t,i){"use strict";function a(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var n=function(){function e(e,t){for(var i=0;i<t.length;i++){var a=t[i];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}return function(t,i,a){return i&&e(t.prototype,i),a&&e(t,a),t}}(),r=i("6ff75de42f89cafb6c75"),o=function(){function e(t){a(this,e),this.$startTime=$("#startTime"),this._init()}return n(e,[{key:"_init",value:function(){this.initStep2Form(),this._timePickerHide()}},{key:"initStep2Form",value:function(){jQuery.validator.addMethod("show_overlap_time_error",function(e,t){return this.optional(t)||!$(t).data("showError")},"所选时间已经有直播了，请换个时间");var e=$("#step2-form");this.validator2=e.validate({onkeyup:!1,rules:{title:{required:!0,maxlength:50,trim:!0,open_live_course_title:!0},startTime:{required:!0,DateAndTime:!0,after_now:!0},length:{required:!0,digits:!0,max:300,min:1,show_overlap_time_error:!0},remark:{maxlength:1e3}}}),(0,r.initEditor)($('[name="remark"]'),this.validator2),e.data("validator",this.validator2),this.dateTimePicker(this.validator2);var t=this;e.find("#startTime").change(function(){t.checkOverlapTime(e)}),e.find("#length").change(function(){t.checkOverlapTime(e)})}},{key:"checkOverlapTime",value:function(e){if(e.find("#startTime").val()&&e.find("#length").val()){var t=1,i={startTime:e.find("#startTime").val(),length:e.find("#length").val(),mediaType:"live"};$.ajax({url:e.find("#length").data("url"),async:!1,type:"POST",data:i,dataType:"json",success:function(e){t=0===e.success}}),e.find("#length").data("showError",t)}}},{key:"dateTimePicker",value:function(e){var t=this.$startTime;t.datetimepicker({format:"yyyy-mm-dd hh:ii",language:document.documentElement.lang,autoclose:!0,endDate:new Date(Date.now()+31536e7)}).on("hide",function(){e.form()}),t.datetimepicker("setStartDate",new Date)}},{key:"_timePickerHide",value:function(){var e=this.$startTime;parent.$("#modal",window.parent.document).on("afterNext",function(){e.datetimepicker("hide")})}}]),e}();t.default=o},"6ff75de42f89cafb6c75":function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});t.initEditor=function(e,t){var i=CKEDITOR.replace("text-content-field",{toolbar:"Task",fileSingleSizeLimit:app.fileSingleSizeLimit,filebrowserImageUploadUrl:$("#text-content-field").data("imageUploadUrl"),filebrowserFlashUploadUrl:$("#text-content-field").data("flashUploadUrl"),allowedContent:!0,height:300});return i.on("change",function(){e.val(i.getData()),t&&t.form()}),i.on("blur",function(){e.val(i.getData()),t&&t.form()}),i}}},["697c4beb0e34534c4c01"]);