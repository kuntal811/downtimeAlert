import React from 'react'
import Swal from 'sweetalert2'
import Cookies from 'universal-cookie'
import Profile from './Profile'
import SidebarLinkItem from './SidebarLinkItem';
import { MainContext } from './Context';
class Sidebar extends React.Component {


    _onLogout = async()=>{
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#A855F7',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Log me out!',
            showLoaderOnConfirm: true,
          }).then(async(result) => {
            if (result.isConfirmed) {
                const cookies = new Cookies();
                cookies.remove('token')
                this.context.updateState(
                    'auth',{isLoggedIn:false,token:false}
                );
            }
          })
    }


    render(){
        const {isSidebarOpen} =  this.props;
        //console.log('sidebar'+isSidebarOpen);
        return (
            <div style={isSidebarOpen?{display:'block'}:{display:'none'}}>
                <div id="sidebar" className="sidebar-container d-flex flex-column justify-content-between">
                    <div>
                        <Profile/>
                        <div className="sidebar-link-item d-flex flex-column">
                            <SidebarLinkItem name="Dashboard" href="/website" icon="bi bi-house" className="active"/>
                            <SidebarLinkItem name="Domain" href="/domain" icon="bi bi-speedometer2"/>
                            <SidebarLinkItem name="SSL" href="/ssl" icon="bi bi-shield"/>
                            <a href="#logout" onClick={this._onLogout}><i className="bi bi-box-arrow-right"/>Logout</a>
                        </div>
                    </div>
                    <div className="mb-4">
                        <p className="copyright my-2 text-center text-muted">&copy;{new Date().getFullYear()} Kuntal Sarkar</p>
                        <div className="social-link text-center">
                            <a className="" href="https://www.linkedin.com/in/kuntal811" target="_blank" rel="noreferrer"><i className="bi bi-linkedin linkedin"/></a>
                            <a className="" href="https://www.github.com/kuntal811" target="_blank" rel="noreferrer"><i className="bi bi-github github"/></a>
                        </div>
                    </div>
                </div>

            </div>
        )
    }
}

Sidebar.contextType = MainContext;
export default Sidebar;
