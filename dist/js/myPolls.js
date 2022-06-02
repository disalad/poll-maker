const trashBtn = document.querySelector(".fa-trash-can");
const shareBtn = document.querySelector(".fa-share-nodes");

trashBtn.addEventListener("click", deletePost);

shareBtn.addEventListener("click", copyUrl2Clipboard);


function copyUrl2Clipboard(ev) {
    const p_id = this.getAttribute("data-poll-id");
    const dWp = location.protocol + "//" + location.host;
    const url = `${dWp}/poll/${p_id}`;

    // Copy url to clipboard
    navigator.clipboard.writeText(url);

    // Show the tooltip
    const tooltip = document.querySelector(".tooltiptext");
    tooltip.classList.add("show");

    // Hide the tooltip after 2 seconds
    setTimeout(() => {
        tooltip.classList.remove("show");
    }, 2000);
}

function deletePost(ev) {
    const p_id = this.getAttribute("data-poll-id");
    console.error(p_id);
    window.location.href = `/poll/${p_id}/delete`;
}