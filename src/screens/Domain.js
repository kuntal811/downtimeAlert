import React, { Component } from 'react'

import './../css/custom.css';
import Header from './../component/Header';
import DomainContent from '../component/DomainContent';
import Footer from './../component/Footer';
import Sidebar from './../component/Sidebar';
export default class Domain extends Component {
    render() {
        return (
            <div>
                <Sidebar/>
                <Header/>
                <DomainContent/>
                {/*<Footer/>*/}
            </div>
        )
    }
}
