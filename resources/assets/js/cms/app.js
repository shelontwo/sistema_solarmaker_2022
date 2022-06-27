import flatpickr from "flatpickr";
import rangePlugin from 'flatpickr/dist/plugins/rangePlugin'

flatpickr('#eventDate', {
	enableTime: true,
	minDate: "today",
	dateFormat: "d/m/Y H:i",
	altFormat: "d/m/Y H:i",
	time_24hr: true,
	disableMobile: true
})

flatpickr('#startDate', {
	enableTime: true,
	altInput: true,
	dateFormat: "Y-m-d H:i",
	altFormat: "d/m/Y H:i",
	disableMobile: true,
	"plugins": [new rangePlugin({ input: "#endDate" })]
});

flatpickr('#inputDate', {
	enableTime: true,
	altInput: true,
	minDate: "today",
	dateFormat: "Y-m-d H:i",
	altFormat: "d/m/Y H:i",
	time_24hr: true,
	disableMobile: true
});

flatpickr('#inputTime', {
	enableTime: true,
	noCalendar: true,
	dateFormat: "H:i",
	time_24hr: true,
	disableMobile: true
});

flatpickr('#inputDateTime', {
	enableTime: true,
	altInput: true,
	dateFormat: "Y-m-d H:i",
	altFormat: "d/m/Y H:i",
	time_24hr: true,
	disableMobile: true
});

tinymce.init({
	selector: 'textarea#full_textarea',
	language: 'pt_BR',
	plugins: 'advlist lists',
	toolbar: 'undo redo bold italic strikethrough | numlist bullist | alignleft aligncenter alignright alignjustify',
	height: "400",
	language_url: '/langs/pt_BR.js',
});

tinymce.init({
	selector: "textarea#full_textarea_banner",
	language: 'pt_BR',
	relative_urls: false,
	paste_data_images: true,
	image_title: true,
	automatic_uploads: true,
	images_upload_url: "/api/upload",
	file_picker_types: "image",
	height: "420",
	language_url: '/langs/pt_BR.js',
	plugins: [
		"advlist autolink lists charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern"
	],
	toolbar1:
		"insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	toolbar2: "print preview media | forecolor backcolor emoticons",
	// override default upload handler to simulate successful upload
	file_picker_callback: function (cb, value, meta) {
		var input = document.createElement("input");
		input.setAttribute("type", "file");
		input.setAttribute("accept", "image/*");
		input.onchange = function () {
			var file = this.files[0];

			var reader = new FileReader();
			reader.readAsDataURL(file);
			reader.onload = function () {
				var id = "blobid" + new Date().getTime();
				var blobCache = tinymce.activeEditor.editorUpload.blobCache;
				var base64 = reader.result.split(",")[1];
				var blobInfo = blobCache.create(id, file, base64);
				blobCache.add(blobInfo);
				cb(blobInfo.blobUri(), { title: file.name });
			};
		};
		input.click();
	}
});