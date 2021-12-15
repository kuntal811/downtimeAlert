import React from 'react'
import {
    NavLink,
    withRouter
  } from "react-router-dom";
class SidebarLinkItem extends React.Component{
    render(){
        let { url} = this.props.match;
        
        return (
            <NavLink to={url+this.props.href} activeClassName="active">
                <i className={this.props.icon}></i>
                {this.props.name}
            </NavLink>
        ) 
    }
}

export default withRouter(SidebarLinkItem);
