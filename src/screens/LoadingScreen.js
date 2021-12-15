import React, { Component } from 'react'
//import Loader from "react-loader-spinner";
import logo from './../images/logoname.svg';
export class LoadingScreen extends Component {
    render() {
        return (
            <div id="loading-container" style={{display:this.props.show?'block':'none'}}>
                <div className="loading">
                    <img src={logo} alt="downtime alert logo" height="100"/>
                    {/*<Loader className="text-center mt-3" type="Bars" color="#A855F7" height={80} width={60} />   */}
                </div>
            </div>
        )
    }
}

export default LoadingScreen
