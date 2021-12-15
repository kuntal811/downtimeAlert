import React from 'react'

export default function FeaturesCard(props) {
    return (
        <div className="card features-card text-center p-3 py-4">
            <img src ={props.image} height="100" className="d-block mx-auto" alt=""/>
            <h3 className="my-3">{props.title}</h3>
            <p>{props.description}</p>
        </div>
    )
}
