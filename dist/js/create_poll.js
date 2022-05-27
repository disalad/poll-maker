const addBtn = document.getElementById('add-option-btn');
const optionsDiv = document.querySelector('.options');

// Set event listener to the add option button
addBtn.addEventListener('click', function () {
    const i = optionsDiv.getElementsByTagName('input').length + 1;
    optionsDiv.insertAdjacentHTML(
        'beforeend',
        `<div class="poll-option mb-3">
            <input type="text" class="form-control" id="inputOption${i}" name="options[]" placeholder="Option ${i}">
            <span class="option-remove-icon">&#10006</span>
        </div>`,
    );
    const span = document.getElementById(`inputOption${i}`).nextElementSibling;
    span.addEventListener('click', removeOptionInput);

    if (i > 2) {
        optionsDiv.classList.add('show');
    }
});

// Set event listener to remove option input buttons
const spans = document.querySelectorAll('.poll-option > .option-remove-icon');
Array.from(spans).forEach(span => {
    span.addEventListener('click', removeOptionInput);
});

function removeOptionInput(ev) {
    this.parentElement.remove();
    const inputs = Array.from(optionsDiv.getElementsByTagName('input'));
    inputs.forEach((el, idx) => {
        idx += 1;
        el.setAttribute('id', `inputOption${idx}`);
        el.setAttribute('name', `options[]`);
        el.setAttribute('placeholder', `Option ${idx}`);
    });
    if (inputs.length <= 2) {
        optionsDiv.classList.remove('show');
    }
}
