import React from 'react';
import moment from "moment";

export default function (props) {
    return <time className={props.className} dateTime={props.dateTime}>
        {moment(Date.parse(props.dateTime), null, 'fr').fromNow()}
    </time>
}
