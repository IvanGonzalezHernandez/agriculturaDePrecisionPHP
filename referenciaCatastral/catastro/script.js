'use strict';

document.addEventListener("DOMContentLoaded", inicio);

function inicio() {
    obtenerDatos();
}

async function obtenerDatos() {
    try {
        const respuesta = await fetch("./datos.json");
        if (respuesta.ok && respuesta.status === 200) {
            console.log("Archivo encontrado");
            const datos = await respuesta.json();
            if (datos) {
                console.log("JSON parseado correctamente");
                buscarParcela(datos);
            } else {
                console.error("Error en la conversión a JSON");
                throw new Error("Error en la conversión a JSON");
            }
        } else {
            console.error("Error en la conexión al archivo");
            throw new Error("Error en la conexión");
        }
    } catch (err) {
        console.error("Ocurrió un error:", err);
    }
}

function buscarParcela(datos) {
    // Elementos del DOM
    let inputReferencia = document.getElementById("referencia");
    let boton = document.getElementById("buscar");
    let resultado = document.getElementById("resultado");



    boton.addEventListener("click", () => {
        // Limpiar el contenido previo
        resultado.innerHTML = "";

        // Obtener la referencia introducida
        const referenciaBuscada = inputReferencia.value.trim();

        // Buscar la parcela en los datos
        const parcela = datos.find(dato => dato.referencia === referenciaBuscada);

        if (parcela) {
            // Mostrar los datos de la parcela. Hay que modificar parcela.propietario para que lo coja del usuario creado y no del JSON
            const detalles = `
                <h3>Detalles de la parcela:</h3>
                <p><strong>Dirección:</strong> ${parcela.direccion}</p>
                <p><strong>Superficie:</strong> ${parcela.superficie}</p>
                <p><strong>Propietario:</strong> ${parcela.propietario}</p>
                <p><strong>Tipo de Inmueble:</strong> ${parcela.tipo_inmueble}</p>
                <p><strong>Valor Catastral:</strong> ${parcela.valor_catastral}</p>
                <p><strong>Descripción:</strong> ${parcela.descripcion}</p>
                <p><strong>Plano:</strong> <a href="${parcela.plano}" target="_blank">Ver plano</a></p>
                <h4>Características:</h4>
                <ul>
                    <li><strong>Antigüedad:</strong> ${parcela.caracteristicas.antiguedad}</li>
                    <li><strong>Estado:</strong> ${parcela.caracteristicas.estado}</li>
                    <li><strong>Accesibilidad:</strong> ${parcela.caracteristicas.accesibilidad}</li>
                    <li><strong>Tipo de Terreno:</strong> ${parcela.caracteristicas.tipo_de_terreno}</li>
                </ul>
                <h4>Limitaciones:</h4>
                <ul>${parcela.limitaciones.map(lim => `<li>${lim}</li>`).join("")}</ul>
            `;
            resultado.innerHTML = detalles;
        } else {
            // Si no se encuentra la parcela
            resultado.textContent = "No se encontró ninguna parcela con la referencia proporcionada.";
        }
    });
}
