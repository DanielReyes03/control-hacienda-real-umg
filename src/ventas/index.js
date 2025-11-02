 // Simulación de cálculo total
    const producto = document.getElementById('producto');
    const cantidad = document.getElementById('cantidad');
    const total = document.getElementById('total');

    const precios = {
        "Parrillada Mixta": 180,
        "Lomo de Res": 125,
        "Costillas BBQ": 150,
        "Bebida Natural": 20,
        "Postre de la Casa": 30
    };

    function calcularTotal() {
        const p = producto.value;
        const c = parseInt(cantidad.value) || 0;
        total.value = p && c ? (precios[p] * c).toFixed(2) : "0.00";
    }

    producto.addEventListener('change', calcularTotal);
    cantidad.addEventListener('input', calcularTotal);