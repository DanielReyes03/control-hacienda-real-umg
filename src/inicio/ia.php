<h2>Asistente IA</h2>

<div id="chat" style="border:1px solid #ccc;padding:10px;height:300px;overflow:auto;"></div>

<textarea id="txt" style="width:100%;height:60px;"></textarea>
<button onclick="enviar()">Enviar</button>

<script>
async function enviar() {
    const msg = document.getElementById("txt").value;
    document.getElementById("txt").value = "";

    const chat = document.getElementById("chat");
    chat.innerHTML += "<p><b>Tú:</b> " + msg + "</p>";

    const res = await fetch("/src/compartido/chat_ia.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mensaje: msg })
    });

    const data = await res.json();
    chat.innerHTML += "<p><b>IA:</b> " + data.output_text + "</p>";

    chat.scrollTop = chat.scrollHeight;
}
</script>
