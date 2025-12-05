"use client";

import {useState} from "react";

export default function EditarPerfil() {
    const [nome, SetNome] = useState("");
    const [email, SetEmail] = useState("")
    const [foto, SetFoto] = useState(null);
    const [preview, SetPreview] = useState(null);
}

function handleFoto(e) {
    const arquivo = e.target.files[0];
    setFoto(arquivo);
}

if (arquivo) {
    const url = URL.createObjectURL(arquivo);
    SetPreview(url);
}

function handleSubmit(e) {
    e.preventDefault();
    //envia para a API
    console.log("Enviando Dados...", {nome, email, foto});
}

return (
    <div>
        <h1> Editar Perfil </h1>

        <form onSubmit={handleSubmit}>
            {/*Foto */ }

            <div> 
                {preview ? (
                    <img 
                    src= {preview}
                    alt="Prévia da foto"
                    width={120}
                    height{120}
                    />
                ) : (
                    <div> Sem foto ainda </div>
                )}

                <input type="file" accept="image/*" onChange={handleFoto} />

                {/* Nome */}

                <label>
                    Nome
                    <input 
                        type="text"
                        value={nome}
                        onChange={e => setNome(e.target.value)}
                    />
                </label>

                {/* Email */}

                <label>
                    E-mail 
                    <input 
                        type="email"
                        value={email}
                        onChange={e=> setEmail(e.target.value)}

                    />
                </label>

                {/* Botoes */}

                <button type="submit"> 
                    Salvar </button>
                <button type="button" onClick={() => window.history.back()}> 
                    Cancelar 
                </button>
            </div>
        </form>
    </div>
)

