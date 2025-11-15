// Chatbot frontend interaction placeholder
async function sendMessage(message){
    const res = await fetch('/actions/chatbot_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({message})});
    return res.json();
}
