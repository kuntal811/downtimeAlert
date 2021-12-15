import React, { Component } from 'react'

import './../css/custom.css';
import Header from '../component/Header';
//import DomainContent from '../component/DomainContent';
//import Footer from '../component/Footer';
//import Sidebar from '../component/Sidebar';
import Login from '../component/Login';
export default class LoginScreen extends Component {
    render() {
        return (
            <div id="content" className="login-screen">
                <Header/>
                <Login/>
            </div>
        )
    }
}
