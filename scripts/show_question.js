document.getElementById('codeForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const code = document.getElementById('codeInput').value;
    const baseURL = "https://node105.webte.fei.stuba.sk/";
    window.location.href = baseURL + code;
});