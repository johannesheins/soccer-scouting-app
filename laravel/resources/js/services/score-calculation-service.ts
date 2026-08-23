import {EvaluationCriteriaGroups} from "@/types/types";

type scores = Record<number, number>;
type CriteriaScore = number[];
export class ScoreCalculationService {
    scores: scores;
    criteriaGroups: EvaluationCriteriaGroups[];
    groupScores: CriteriaScore;
    totalScore: number;

    constructor(scores: Record<number, number>, criteriaGroups: EvaluationCriteriaGroups[]) {
        this.scores = scores;

        this.criteriaGroups = criteriaGroups;
        this.groupScores = [];
        this.totalScore = 0;

        this.calculate()
    }

    public calculate() {
        this.criteriaGroups.forEach(group => {
            this.groupScores[group.id] = 0;
            group.evaluation_criteria.forEach(evaluationCriteria => {
                const score = this.scores[evaluationCriteria.id] ?? 0
                this.groupScores[group.id] += score;
                this.totalScore += score;
            })
        })
    }

    public getTotalScore(){
        return this.totalScore;
    }

    public getGroupScore(groupId: number){
        return this.groupScores[groupId];
    }
}
