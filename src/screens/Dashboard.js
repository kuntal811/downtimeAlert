import React, { Component } from 'react'
import {
    Switch,
    Route,
    withRouter
  } from "react-router-dom";

  
import './../css/custom.css';
import Header from './../component/Header';
import DashboardContent from '../component/DashboardContent';
//import Footer from './../component/Footer';
import Sidebar from './../component/Sidebar';
import DomainContent from '../component/DomainContent';
import SslContent from '../component/SslContent';
import ReportContent from '../component/ReportContent';

import { MainContext } from './../component/Context'
import { Redirect } from 'react-router-dom';
import MonitorContent from '../component/MonitorContent';

class Dashboard extends Component {
    constructor(props) {
        super(props)
    
        this.state = {
            isSidebarOpen: window.innerWidth > 900?  true: false,
        }
    }
    toggleSidebar = () =>{
        this.setState({isSidebarOpen: !this.state.isSidebarOpen});
    }
    render() {
        if(!this.context.state.auth.isLoggedIn) return <Redirect to="/login"/>;
        let { path } = this.props.match;
        //console.log(this.props.match);
        return (
              <>
                <Sidebar isSidebarOpen={this.state.isSidebarOpen} />
                <Header isSidebarOpen={this.state.isSidebarOpen} toggleSidebar={this.toggleSidebar}/>
                <div id="content" style={this.state.isSidebarOpen?{marginLeft:'250px'}:{marginLeft:'0px'}}>
                    
                  <Switch>
                    <Route exact path={path+"/website"}>
                      <DashboardContent/>
                    </Route>
                    <Route exact path={path+"/domain"}>
                      <DomainContent/>
                    </Route>

                    <Route exact path={path+"/ssl"}>
                      <SslContent/>
                    </Route>

                    <Route exact path={path+"/report"}>
                      <ReportContent/>
                    </Route>

					          <Route exact path={path+"/"}>
                      <Redirect to={path+"/website"}/>
                    </Route>

                    <Route path={path+"/website/:monitorId"}>
                      <MonitorContent/>
                    </Route>

                    <Route>
                      <p>404</p>
                    </Route>

                  </Switch>
                </div>
                {/*
                <Footer isSidebarOpen={this.state.isSidebarOpen}/>
                */}
              </>

        )
    }
}

Dashboard.contextType = MainContext;

export default withRouter(Dashboard)