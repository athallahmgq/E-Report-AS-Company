const { ClassicEditor, Essentials, Bold, Italic, Font, Paragraph } = CKEDITOR;

const editorConfig = {
  plugins: [Essentials, Bold, Italic, Font, Paragraph],
  toolbar: [
    "undo",
    "redo",
    "|",
    "bold",
    "italic",
    "|",
    "fontSize",
    "fontFamily",
    "fontColor",
    "fontBackgroundColor",
  ],
};

// Initialize CKEditor for the first textarea
ClassicEditor.create(document.querySelector("#editor"), editorConfig)
  .then(/* ... */)
  .catch(/* ... */);

// Initialize CKEditor for the second textarea
ClassicEditor.create(document.querySelector("#content"), editorConfig)
  .then(/* ... */)
  .catch(/* ... */);
