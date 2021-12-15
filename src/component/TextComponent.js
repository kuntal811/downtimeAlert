import React from 'react'

function TextComponent(pros) {
    return (
        <div className="text-component p-3">
            <h2>{pros.heading}</h2>
            <p>{pros.text}</p>
        </div>
    )
}

export default TextComponent;
