import React from 'react'
import profilePhoto from '../images/profile.svg';
import jwt_decode from "jwt-decode";
import { MainContext } from './Context';

class Profile extends React.Component {

    render(){
        let token = this.context.state.auth.token;
        let decoded = jwt_decode(token);
        //console.log(decoded);
        return (
            <div className="profile text-center mx-auto my-4">
                    <div className="profile-photo d-inline-block">
                        <img src={profilePhoto} alt="man"/>
                    </div>
                    <h3 className="mt-3 text-capitalize">{decoded.user.name}</h3>
                    <p>{decoded.user.email}</p>
                </div>
        )
    }
}
Profile.contextType = MainContext;
export default Profile
