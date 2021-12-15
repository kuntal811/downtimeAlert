import React from 'react'

class DomainMonitorCard extends React.Component {
    
    render(){
        const {id,name,organization,expire_on,registered_on,remind_before_days,onDelete} = this.props;
        return (
            <div className="col-12 col-md-6 col-lg-4 mb-3">
                <div className="card domain-monitor-card">
                    <h5 className="card-header text-black">{/*<i className="text-primary bi bi-globe"/>*/}
                    <img className="favicon" src={(this.props.protocol+'://'+this.props.name+'/favicon.ico')} alt=""/>
                    {" "+name}</h5>
                    <div className="card-body d-flex flex-column align-items-between justify-content-evenly">
                        <div className="row">
                            <div className="col-6 d-flex flex-column text-center">
                                <div className="d-flex justify-content-center align-items-center">
                                    <i className="bi bi-building text-primary icon-md"/>
                                    <p className="monitor-heading-text ps-1 d-inline">Organization</p> 
                                </div>
                                <p className="monitor-text">{organization?organization:'- - - '}</p>
                            </div>
                            <div className="col-6 d-flex flex-column justify-content-start align-items-center">
                                <div className="d-flex justify-content-center align-items-center">
                                    <i className="bi bi-calendar4-week text-primary icon-md"/>
                                    <p className="monitor-heading-text ps-1">Registered On</p>
                                </div>
                                <p className="monitor-text">{registered_on?registered_on:'- - -'}</p>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col-6 d-flex flex-column text-center">
                                <div className="d-flex justify-content-center align-items-center">
                                    <i className="bi bi-calendar4-week text-primary icon-md"/>
                                    <p className="monitor-heading-text ps-1 d-inline">Remind Before</p> 
                                </div>
                                <p className="monitor-text text-truncate">{remind_before_days?remind_before_days+' days':'- - -'}</p>
                            </div>
                            <div className="col-6 d-flex flex-column justify-content-start align-items-center">
                                <div className="d-flex justify-content-center align-items-center">
                                    <i className="bi bi-calendar4-week text-primary icon-md"/>
                                    <p className="monitor-heading-text ps-1">Expire On</p>
                                </div>
                                <p className="monitor-text">{expire_on?expire_on:'- - -'}</p>
                            </div>
                        </div>

                        {/*
                        <div className="row">
                            <div className="col-12">
                                <div className="form-check form-switch">
                                    <label className="form-check-label" htmlFor="flexSwitchCheckChecked">Alert On</label>
                                    <input className="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked={this.props.alert_status}/>
                                </div>
                            </div>
                        </div>
                        */}
                        <div className="row">
                            <div className="col-6 d-flex flex-column text-center justify-content-center">
                                <span className={"badge "+ (this.props.fetched?"bg-success":"bg-danger")}>{this.props.fetched?"Fetched":"Not found"}</span>
                            </div>
                            <div className="col-6 d-flex flex-column justify-content-center align-items-center">
                                <p className="monitor-heading-text">Delete</p>
                                <button className="btn d-block" onClick={()=>onDelete(id)}><i className="bi bi-trash text-danger"/></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default DomainMonitorCard
