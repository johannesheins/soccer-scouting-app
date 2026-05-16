//TODO Adjust name
//TODO Add a title when disabled with e.g No right to search a player
import React from 'react';
import {cn} from "@/lib/utils";

export default function Block({children, active, title}: {children: React.ReactNode, active?: boolean, title?: string}){
    return (
        <div className="grid grid-cols-1 grid-rows-1 h-full w-full">
            {!active && <div title={title} className="z-10 col-start-1 row-start-1">

            </div>}
            <div className={cn(active ? '' : 'disabled',"col-start-1 row-start-1")}>
                {children}
            </div>
        </div>
    )
}
