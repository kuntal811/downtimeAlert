import React from 'react'

function ItemCount(props) {
    return (
        <div className={("d-flex justify-content-stretch mb-3 mb-lg-0 "+props.columns)}>
            <div className={"item-count-title small-box d-flex flex-column  justify-content-between align-items-stretch w-100 py-4 shadow-md "+props.className}>
                    <h3 className={"text-center "+props.headerLabelClass}>{props.title}</h3>
                    <p className={"text-center "+props.countClass}>{props.count}</p>
            </div>
        </div>
    )
}

export default ItemCount;
