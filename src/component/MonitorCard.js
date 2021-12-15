import React from 'react'
import { Link } from 'react-router-dom';

class MonitorCard extends React.Component {
    render(){
        const {url,name,performance,uptime,lastChecked,lastIncident, status} = this.props;
        let incident_time = new Date(lastIncident);
        return (
            <div className="col-12 col-md-6 col-lg-4 mb-3">
                <Link to={url}>
                    <div className="card monitor-card h-100">
                        <h5 className="card-header text-black text-capitalize">
                            {" "+name}
                            <span className={"badge text-"+(status==='1'?'success':'danger')}>{status==='1'?'active':(performance?'Down':'')}</span>
                        </h5>
                        <div className="card-body d-flex flex-column align-items-between">
                            <div className="row">
                                <div className="col-6 d-flex flex-column text-center">
                                    <i className="text-success bi bi-speedometer2 icon-md "/>
                                    <p className="monitor-heading-text">Performance</p>
                                    <p>{performance?performance:'- - -'}</p>
                                </div>
                                <div className="col-6 d-flex flex-column justify-content-center align-items-center">
                                    <i className="text-success bi bi-clock icon-md"/>
                                    <p className="monitor-heading-text">Uptime</p>
                                    <p>{parseFloat(uptime).toFixed(2)}%</p>
                                </div>
                            </div>
        
                            <div className="row">
                                <div className="col-6 d-flex flex-column text-center">
                                    <p className="monitor-heading-text">Last Check</p>
                                    <p>
                                        {
                                            performance?
                                                lastChecked >= 3600?
                                                    `${parseInt(lastChecked/3600)}h ${parseInt((lastChecked%3600)/60)}m ${(lastChecked%3600)%60}s ago`
                                                :
                                                    lastChecked >= 60?
                                                        `${parseInt(lastChecked/60)}m ${lastChecked%60}s ago`
                                                    :
                                                        `${lastChecked}s ago`
                                            :
                                                'yet to check'
                                        } 
                                        
                                    </p>
                                </div>
                                <div className="col-6 d-flex flex-column justify-content-center align-items-center">
                                    <p className="monitor-heading-text">Last Incident</p>
                                    {lastIncident?
                                        <p className="last-incident-text">
                                            
                                            {incident_time.toDateString()}<br/>
                                            {incident_time.getHours() > 12 ? (incident_time.getHours()-12): incident_time.getHours()}:
                                            {incident_time.getMinutes()}:{incident_time.getSeconds()}
                                            {incident_time.getHours() > 12 ? ' PM':' AM'}
                                        </p>
                                    :
                                    <p>- - -</p>
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        );
    }
}

export default MonitorCard
