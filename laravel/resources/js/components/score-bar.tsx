import { Input } from "@/components/ui/input";
import {MinusCircle, PlusCircle, Star as StarIcon, StarHalf} from "lucide-react";
import {useState} from "react";
import {cn} from "@/lib/utils";

export default function ScoreBar({name, className, value = 0, onChange}: {
    name: string,
    className?: string,
    value?: number,
    onChange?: (value: number) => void
}) {
    const [currentValue, setCurrentValue] = useState(value);

    function update(next: number) {
        setCurrentValue(next);
        onChange?.(next);
    }

    const variants: ('zero' | 'half' | 'full')[] = [];

    if(currentValue > 0){
        const max = currentValue % 2 > 0 ? currentValue + 0.5 : currentValue;


        for(let i: number = 1; i <= max; i++){
            if(i >= currentValue && ((currentValue * 2) % 2) > 0){
               variants.push('half');
               continue;
            }
            variants.push('full');
        }
    }

    while(variants.length < 10){
        variants.push('zero');
    }

    return (
        <>
            <div className={cn(className, "grid grid-cols-12 grid-rows-1 gap-2 justify-items-center items-center")}>
                <MinusCircle className="size-3" onClick={() => update(Math.max(0, currentValue - 0.5))}/>
                {variants.map((v, key) =>
                    <div key={key} className="w-full">
                        <Star variant={v} />
                    </div>
                )}
                <PlusCircle className="size-3" onClick={() => update(Math.min(10, currentValue + 0.5))}/>
            </div>
            <Input type="hidden" name={name} value={currentValue} min="0" max="10" readOnly />
        </>
    )
}


function Star({ variant, className }: { variant: 'zero' | 'half' | 'full', className?: string }) {
    switch(variant){
        case 'zero':
            return <StarIcon className={cn(className, "w-full fill-muted-foreground stroke-0")}/>
        case 'half':
            return (
                <div className={cn(className, "grid grid-rows-1 grid-cols-1 w-full")}>
                    <StarHalf className="w-full col-start-1 row-start-1 z-10 fill-amber-300 stroke-0"/>
                    <StarHalf className="w-full col-start-1 row-start-1 z-20 rotate-y-180 fill-muted-foreground stroke-0"/>
                </div>
            );
        case 'full':
            return <StarIcon className={cn(className, "w-full fill-amber-300 stroke-0")}/>
    }
}
