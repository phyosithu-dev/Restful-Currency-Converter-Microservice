let form = document.getElementById("submitData");
let response_UI = document.getElementById("response");
async function handleSubmit(e) {
    e.preventDefault();
    let data = new FormData(e.target);
    console.log(data.get("currency"));
    fetch('main.php', {
        method: 'POST',
        body: data
    })
        .then(response => response.text())
        .then(data => {
            response_UI.value = data;
            // Process the JSON response
            console.log(data);
        })
        .catch(error => {
            // Handle any errors that occurred during the request
            console.error(error);
        });
}
form.addEventListener("submit", handleSubmit)