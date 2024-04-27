// Získať selectbox
var selectBox = document.getElementById('questionType');
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


document.getElementById("questionForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Zabrániť štandardnému odosielaniu formulára

    // Získať dáta z formulára
    var formData = new FormData(this);

    // Priradenie aktuálneho dátumu do skrytého poľa
    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
    formData.append("creationDate", formattedDate);

    // Odošlite dáta na server pomocou POST requestu
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_open_question.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Spracovať odpoveď zo servera, ak je potrebné
            console.log(xhr.responseText);
        }
    };
    xhr.send(formData);
});
