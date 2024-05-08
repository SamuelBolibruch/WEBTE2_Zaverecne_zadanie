// Získať selectbox
var selectBox = document.getElementById('questionType');
const messageField = document.getElementById('message');
const messageDiv = document.getElementById('message-div');
// Získať oba divy
var answerQuestionContainer = document.getElementById('answerQuestionContainer');
var openQuestionContainer = document.getElementById('openQuestionContainer');

// Funkcia na zobrazenie správneho divu na základe vybranej možnosti
function showSelectedOption() {
    // Ak je vybratá prvá možnosť (value = 1)
    if (selectBox.value === '1') {
        answerQuestionContainer.style.display = 'block'; // Zobraziť prvý div
        openQuestionContainer.style.display = 'none'; // Skryť druhý div
    } else { // Ak je vybratá druhá možnosť (value = 2)
        answerQuestionContainer.style.display = 'none'; // Skryť prvý div
        openQuestionContainer.style.display = 'block'; // Zobraziť druhý div
    }
}

// Zavolať funkciu pri zmene v selectboxe
selectBox.addEventListener('change', showSelectedOption);
// Zavolať funkciu pri načítaní stránky na nastavenie počiatočného stavu
showSelectedOption();

document.getElementById("addOptionButton").addEventListener("click", function () {
    var answerOptions = document.getElementById("answerOptions");
    var optionCount = answerOptions.getElementsByClassName("answer-option").length;
    var numOfAnswersField = document.getElementById("numOfAnswers1");
    var numOfAnswers = parseInt(numOfAnswersField.value);

    numOfAnswers++;

    numOfAnswersField.value = numOfAnswers;

    // Vytvorenie nového divu pre ďalšiu možnosť odpovede
    var newOptionDiv = document.createElement("div");
    newOptionDiv.classList.add("answer-option");

    // Vytvorenie nového labelu
    var newLabel = document.createElement("label");
    newLabel.htmlFor = "answer" + (optionCount + 1);
    newLabel.textContent = "Možnosť " + (optionCount + 1) + ":";

    // Vytvorenie nového inputu pre odpoveď
    var newInput = document.createElement("input");
    newInput.type = "text";
    newInput.id = "answer" + (optionCount + 1);
    newInput.name = "answer" + (optionCount + 1);
    newInput.required = true;

    // Vytvorenie nového inputu pre označenie správnej možnosti
    var newCheckbox = document.createElement("input");
    newCheckbox.type = "checkbox";
    newCheckbox.id = "correctAnswer" + (optionCount + 1);
    newCheckbox.name = "correctAnswer" + (optionCount + 1);

    // Vytvorenie nového labelu pre označenie správnej možnosti
    var newCheckboxLabel = document.createElement("label");
    newCheckboxLabel.htmlFor = "correctAnswer" + (optionCount + 1);
    newCheckboxLabel.textContent = "Správna možnosť";

    // Pridanie vytvorených prvkov do nového divu
    newOptionDiv.appendChild(newLabel);
    newOptionDiv.appendChild(newInput);
    newOptionDiv.appendChild(newCheckbox);
    newOptionDiv.appendChild(newCheckboxLabel);

    // Pridanie nového divu do zoznamu možností odpovedí
    answerOptions.appendChild(newOptionDiv);
});


document.getElementById("closedQuestionForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Zabrániť štandardnému odosielaniu formulára

    // Priradenie aktuálneho dátumu do skrytého poľa
    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
    document.getElementById('creationDate1').value = formattedDate;

    var formData = new FormData(this);
    formData.append('userEmail', document.getElementById('userEmail').value);  // Pridanie emailu vybraného používateľa


    // Odošlite dáta na server pomocou POST requestu
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_closed_question.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Spracovať odpoveď zo servera, ak je potrebné
            messageDiv.style.display = 'block';
            messageField.innerHTML = 'Úloha bola úspešne vytvorená.';
            console.log(xhr.responseText);

            // Vyčistiť vstupné polia pre odpovede
            var answerOptions = document.getElementById("answerOptions");
            while (answerOptions.firstChild) {
                answerOptions.removeChild(answerOptions.firstChild);
            }

            // Zostaviť iba dva vstupné polia pre odpovede
            for (var i = 0; i < 2; i++) {
                var newOptionDiv = document.createElement("div");
                newOptionDiv.classList.add("answer-option");
            
                var newLabel = document.createElement("label");
                newLabel.htmlFor = "answer" + (i + 1);
                newLabel.textContent = "Možnosť " + (i + 1) + ":";
            
                var newInput = document.createElement("input");
                newInput.type = "text";
                newInput.id = "answer" + (i + 1);
                newInput.name = "answer" + (i + 1);
                newInput.required = true;
            
                var newCheckbox = document.createElement("input");
                newCheckbox.type = "checkbox";
                newCheckbox.id = "correctAnswer" + (i + 1);
                newCheckbox.name = "correctAnswer" + (i + 1);
            
                var newCheckboxLabel = document.createElement("label");
                newCheckboxLabel.htmlFor = "correctAnswer" + (i + 1);
                newCheckboxLabel.textContent = "Správna možnosť";
            
                // Přidání mezery mezi checkboxem a popiskem "Správna možnost"
                var space = document.createTextNode("\u00A0"); // \u00A0 je nezlomitelná mezera
                newOptionDiv.appendChild(newLabel);
                newOptionDiv.appendChild(newInput);
                newOptionDiv.appendChild(newCheckbox);
                newOptionDiv.appendChild(space);
                newOptionDiv.appendChild(newCheckboxLabel);
            
                answerOptions.appendChild(newOptionDiv);
            }
            
            

            // Nastaviť počet odpovedí na 2
            document.getElementById("numOfAnswers1").value = 2;

            document.getElementById("question1").value = "";
            document.getElementById("subject1").value = "";
        }
    };

    // formData.forEach(function (value, key) {
    //     console.log(key + ": " + value);
    // });

    xhr.send(formData);
});

document.getElementById("openQuestionForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Zabrániť štandardnému odosielaniu formulára

    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
    document.getElementById('creationDate').value = formattedDate;
    // Získať dáta z formulára
    var formData = new FormData(this);

    // Odošlite dáta na server pomocou POST requestu
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_open_question.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Spracovať odpoveď zo servera, ak je potrebné
            messageDiv.style.display = 'block';
            messageField.innerHTML = 'Úloha bola úspešne vytvorená.';
            console.log(xhr.responseText);
            // Nastaviť hodnoty na prázdne
            document.getElementById("question").value = "";
            document.getElementById("subject").value = "";
            document.querySelector('input[name="answerDisplay"]:checked').checked = false;
        }
    };

    // formData.forEach(function (value, key) {
    //     console.log(key + ": " + value);
    // });

    xhr.send(formData);
});
