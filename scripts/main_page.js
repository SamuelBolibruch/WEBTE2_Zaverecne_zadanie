const button = document.getElementById('tryButton');
const modal = document.getElementById("qrCodeModal");
const questionCode = document.getElementById('questionCode');
const questionAdress = document.getElementById('questionAdress');
const questionQrCode = document.getElementById('questionQrCode');

var select1 = document.querySelector("select[name='select1']");
var select2 = document.querySelector("select[name='select2']");

document.addEventListener("DOMContentLoaded", function () {
    function updateTable() {
        var selectedSubject = select1.value;
        var selectedDate = select2.value;

        // Získanie všetkých riadkov tabuľky
        var rows = document.querySelectorAll("#questionsTable tbody tr");

        // Prechádzanie cez každý riadok a zobrazovanie/skrývanie riadkov podľa výberu v select boxoch
        rows.forEach(function (row) {
            var cells = row.cells;
            var rowSubject = cells[2].textContent; // Index 2 pre stĺpec s predmetom
            var rowDate = cells[5].textContent;

            // Ak je vybratý predmet zhodný s predmetom riadku a zároveň je vybraný dátum zhodný s dátumom riadku alebo je vybratý prázdny dátum
            if ((selectedSubject === rowSubject || selectedSubject === "") && (selectedDate === rowDate || selectedDate === "")) {
                row.style.display = ""; // Zobraziť riadok
            } else {
                row.style.display = "none"; // Skryť riadok
            }
        });
    }

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // Získání odpovědi ze serveru
            var response = JSON.parse(this.responseText);

            // Vložení dat do tabulky
            var tableBody = document.querySelector("#questionsTable tbody");
            response.forEach(function (question) {
                var row = tableBody.insertRow();
                var cell1 = row.insertCell(0);
                cell1.classList.add('align-to-left');
                var cell2 = row.insertCell(1);
                var cell7 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                var cell4 = row.insertCell(4);
                var cell5 = row.insertCell(5);
                var cell6 = row.insertCell(6);
                var cell8 = row.insertCell(7);

                cell1.textContent = question.question;
                cell2.textContent = question.id;
                cell2.classList.add('question-id-cell'); // Pridanie triedy pre stĺpec s id otázky
                cell7.textContent = question.subject;
                cell8.textContent = question.user_email;
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = question.is_active;

                checkbox.addEventListener('change', function () {
                    Swal.fire({
                        title: 'Naozaj chcete zmeniť stav tejto otázky?',
                        text: "Táto akcia môže ovplyvniť údaje v systéme!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Áno, zmeniť!',
                        cancelButtonText: 'Zrušiť'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Ak je potvrdené, vykonajte akciu
                            var questionId = question.id;
                            var questionState = checkbox.checked;
                
                            // Vytvoriť požiadavku
                            var xhr = new XMLHttpRequest();
                
                            // Nastaviť metódu a URL
                            xhr.open("PUT", "edit_question_active_state.php", true);
                            xhr.setRequestHeader("Content-Type", "application/json");
                
                            xhr.onreadystatechange = function () {
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
                        } else {
                            // Ak je potvrdenie zrušené, vráťte pôvodný stav checkboxu
                            checkbox.checked = !checkbox.checked;
                        }
                    });
                });
                

                cell3.appendChild(checkbox);
                cell3.classList.add('center-align'); // Pridanie triedy pre zarovnanie na stred

                cell4.textContent = question.question_type;
                cell5.textContent = question.creation_date;

                var removeImage = document.createElement('img');
                removeImage.src = 'images/remove.png'; // Umiestnite obrázok do adresára, kde je váš HTML súbor
                removeImage.classList.add('remove-image'); // Pridanie triedy remove-image
                removeImage.classList.add('question-id-cell');
                cell6.appendChild(removeImage);

                removeImage.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Naozaj chcete vymazať túto otázku?',
                        text: "Tento úkon je nevratný!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Áno, vymazať!',
                        cancelButtonText: 'Zrušiť'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            var questionId = question.id;
                            var user_email = question.user_email;
                                            
                            var xhr = new XMLHttpRequest();

                            xhr.open("DELETE", "database_services/delete_question.php?id=" + questionId + "&email=" + encodeURIComponent(user_email), true);

                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    // Spracovať odpoveď zo servera
                                    console.log(xhr.responseText);
                                    // Ak chcete aktualizovať zobrazenie tabuľky, môžete sem vložiť kód
                                    location.reload();

                                }
                            };
                        
                            // Odošleme požiadavku 
                            xhr.send();
                        }
                    });
                });
                

                cell2.addEventListener('click', function () {
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

    // Nastavenie listenerov na zmeny v select boxoch
    select1.addEventListener("change", updateTable);
    select2.addEventListener("change", updateTable);

    // Volanie funkcie updateTable na začiatku, aby sa tabuľka zobrazila podľa predvolených hodnôt select boxov
    updateTable();
});