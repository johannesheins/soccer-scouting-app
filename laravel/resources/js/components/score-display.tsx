export default function ScoreDisplay({currentValue, maxScore}: {currentValue: number, maxScore: number}) {
    return (
        <div className="flex flex-row min-w-12 gap-1 items-end">
            <span className="text-end text-sm tabular-nums text-foreground">
                {currentValue}
            </span>
            <span className="text-right text-xs tabular-nums text-foreground">
                / {maxScore}
            </span>
        </div>
    )
};
