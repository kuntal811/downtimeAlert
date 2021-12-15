import React from 'react'
import Logo from './../images/logoname.svg'

import { MainContext } from './../component/Context'
import { withRouter } from 'react-router';

class Header extends React.Component {
    render(){
        const { isSidebarOpen } = this.props;
        const { isLoggedIn } = this.context.state.auth;
        return (
            (isLoggedIn && !(this.props.match.path==="/"))?
            <div className="header d-flex justify-content-between" style={isSidebarOpen?{marginLeft:'250px'}:{marginLeft:'0px'}}>
                <div className="header-left">
                    <button className="btn btn-custom" onClick={this.props.toggleSidebar}>
                        <i className="bi bi-list" title="Menu"></i>
                    </button>
                </div>
                {/*
                <div className="header-right">
                    <button className="btn btn-custom">
                    <i className="bi bi-bell" title="Notification"></i>
                    </button>
                </div>*/}
            </div>
            :
            <div className="header d-flex justify-content-between">
                <div className="header-left">
                    <div id="logo">
                        <a href="/">
                            <img src={ Logo } alt="downtime alert logo" height="50"/>
                        </a>
                    </div>
                </div>
                <div className="header-right">
                </div>
            </div>

        )
}
}

Header.contextType = MainContext;

export default withRouter(Header);
