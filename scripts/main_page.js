const button = document.getElementById('tryButton');
const modal = document.getElementById("qrCodeModal");
const questionCode = document.getElementById('questionCode');
const questionAdress = document.getElementById('questionAdress');
const questionQrCode = document.getElementById('questionQrCode');


document.addEventListener("DOMContentLoaded", function() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Získání odpovědi ze serveru
            var response = JSON.parse(this.responseText);
            
            // Vložení dat do tabulky
            var tableBody = document.querySelector("#questionsTable tbody");
            response.forEach(function(question) {
                var row = tableBody.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                cell1.textContent = question.question;
                cell2.textContent = question.id;
                cell2.classList.add('question-id-cell'); // Pridanie triedy pre stĺpec s id otázky

                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = question.is_active;

                checkbox.addEventListener('change', function() {
                    var questionId = question.id;
                    var questionState = checkbox.checked;
                
                    // Vytvoriť požiadavku
                    var xhr = new XMLHttpRequest();
                
                    // Nastaviť metódu a URL
                    xhr.open("PUT", "edit_question_active_state.php", true);
                    xhr.setRequestHeader("Content-Type", "application/json");
                
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Spracovať odpoveď zo servera
                            console.log(xhr.responseText);
                        }
                    };
                
                    var data = {
                        question_id: questionId,
                        question_state: questionState
                    };
                
                    var jsonData = JSON.stringify(data);
                    xhr.send(jsonData);
                });

                cell3.appendChild(checkbox);
                cell3.classList.add('center-align'); // Pridanie triedy pre zarovnanie na stred

                cell4.textContent = question.question_type;
                cell5.textContent = question.creation_date;

                cell2.addEventListener('click', function() {
                    // Nastavenie textu pre questionCode a questionAdress
                    questionCode.textContent = question.id;
                    questionAdress.textContent = "https://node20.webte.fei.stuba.sk/" + question.id;
                    questionQrCode.innerHTML = '';
                    var qrcode = new QRCode(questionQrCode, {
                        text: "https://node20.webte.fei.stuba.sk/" + question.id,
                        width: 280, // Šírka QR kódu (voliteľné)
                        height: 280 // Výška QR kódu (voliteľné)
                    });
                    // Zobrazenie modálneho okna
                    modal.style.display = "flex";
                });
            });
            $('#questionsTable').DataTable();
        }
    };
    xhr.open("POST", "get_users_questions.php", true);
    xhr.send();

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none"; // Skryť modálne okno
        }
    }
});