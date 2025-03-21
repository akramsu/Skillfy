document.addEventListener("DOMContentLoaded", function () {
  const faqItems = document.querySelectorAll(".faq-item");

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");

    question.addEventListener("click", () => {
      faqItems.forEach((otherItem) => {
        if (otherItem !== item && otherItem.classList.contains("active")) {
          otherItem.classList.remove("active");
        }
      });

      item.classList.toggle("active");
    });
  });

  const playButton = document.querySelector(".play-button");
  const videoContainer = document.querySelector(".video-container");

  playButton.addEventListener("click", () => {
    videoContainer.style.backgroundColor = "#333";
    playButton.style.display = "none";

    const message = document.createElement("div");
    message.textContent = "Video would play here";
    message.style.position = "absolute";
    message.style.top = "50%";
    message.style.left = "50%";
    message.style.transform = "translate(-50%, -50%)";
    message.style.color = "white";
    message.style.fontSize = "1.5rem";

    videoContainer.appendChild(message);
  });
});
