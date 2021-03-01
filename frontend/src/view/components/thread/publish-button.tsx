import React from 'react';
import './publish-button.scss';

export function PublishThreadButton (spec:{
    onClick:() => void;
}) {
    return <div className="thread-publish-button" onClick={spec.onClick}>
        <p>发文</p>
        <p>发贴</p>
    </div>;
}