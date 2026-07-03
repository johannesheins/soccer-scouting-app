import { useState } from "react";
import { Input } from "@/components/ui/input";
import { Slider } from "@/components/ui/slider";
import { cn } from "@/lib/utils";

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

    return (
        <div className={cn(className, "flex items-center gap-3")}>
            <Slider
                className="flex-1"
                value={[currentValue]}
                onValueChange={([next]) => update(next)}
                min={0}
                max={10}
                step={1}
            />
            <span className="w-6 text-right text-sm tabular-nums text-muted-foreground">{currentValue}</span>
            <Input type="hidden" name={name} value={currentValue} min="0" max="10" readOnly />
        </div>
    )
}
