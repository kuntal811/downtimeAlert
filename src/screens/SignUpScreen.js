import React, { Component } from 'react'

import './../css/custom.css';
import Header from '../component/Header';
//import DomainContent from '../component/DomainContent';
//import Footer from '../component/Footer';
//import Sidebar from '../component/Sidebar';
import SignUp from '../component/SignUp';
export default class SignUpScreen extends Component {
    render() {
        return (
            <div id="content" className="login-screen">
                <Header isLoggedIn={this.props.isLoggedIn} />
                <SignUp/>
            </div>
        )
    }
}
