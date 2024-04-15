import {createContext, useState} from "react";
import {useQuery} from "react-query";
import ScormService from "../../core/services/scorm.service";
import useComputed from "../../core/hooks/useComputed";

/**
 * Scorm context
 */
export const ScormContext = createContext({})


const ScormContextWrapper = ({children}) => {

    /**
     * COMPONENT STATE
     */
    const [scormUUID] = useState(window?.scorm_uuid || null)
    const [activeSco, setActiveSco] = useState(null);
    /**
     * TRACKING ID
     */
    const trackingId = useComputed(() => {
        const urlSearchString = window.location.search;
        const params = new URLSearchParams(urlSearchString);
        return params.get('tracking_id')
    }, [])

    /**
     * FETCHING THE SCORM DETAILS - REACT QUERY
     */
    const {data: scormDetails, isLoading} = useQuery([{scormUUID, trackingId}], () => {
        if (scormUUID) {
            return ScormService.getDetails(scormUUID, trackingId)
        }
        return null;
    }, {
        onSuccess(data) {
            const fistsco = data?.scos[0];
            if (fistsco?.entry_url) {
                setActiveSco(data?.scos[0] || null)
            } else {
                setActiveSco(fistsco?.children[0])
            }
        }
    })

    /**
     * SCORM VERSION
     */
    const scormVersion = useComputed(() => {
        return scormDetails?.version
    }, [scormDetails])

    /**
     * SCORM PREVIOUS TRACK
     */
    const scormTracking = useComputed(()=>{
        return activeSco?.tracking || null
    },[activeSco])


    return (
        <>
            <ScormContext.Provider value={{
                scormUUID,
                scormDetails,
                isLoading,
                activeSco,
                scormVersion,
                setActiveSco,
                trackingId,
                scormTracking
            }}>
                {children}
            </ScormContext.Provider>
        </>
    )
}

export default ScormContextWrapper

