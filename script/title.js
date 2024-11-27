let titleText = " A'S Company";
let speed = 300;

function scrollTitle() {
  document.title = titleText;
  titleText = titleText.substring(1) + titleText[0];
  setTimeout(scrollTitle, speed);
}

scrollTitle();
