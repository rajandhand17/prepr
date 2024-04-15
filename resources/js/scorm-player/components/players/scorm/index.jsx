import React from 'react'
import useScormContext from "../../../core/hooks/useScormContext";
import useScormApi from "../../../core/hooks/useScormApi";
import './index.scss';

const ScormPlayer = () => {

    /**
     * HOOKS
     */
    const {activeSco, scormVersion, trackingId, scormTracking} = useScormContext()
    const {isPlayerReady} = useScormApi(activeSco, scormVersion, trackingId, scormTracking)

    return (
        <>
            {
                activeSco?.entry_url && isPlayerReady ?
                    <iframe src={activeSco?.entry_url} className={'prepr-labs-scorm-player-iframe'}/> : ''
            }
        </>
    )
}

export default ScormPlayer
