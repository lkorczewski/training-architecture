<?php

namespace Domain;

use Domain\Exceptions\ApproverNotAllowed;
use Domain\Exceptions\NotAllowedToChangeOpinion;
use Domain\Values\Value;

class Proposal
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    private string $valueName;
    private Value $originalValue;
    private Value $proposedValue;
    private string $author;
    private array $approvals;  // role => status
    private string $status;

    public function __construct(
        string $valueName,
        Value $originalValue,
        Value $proposedValue,
        string $author,
        array $approvers
    ) {
        $this->valueName = $valueName;
        $this->originalValue = $originalValue;
        $this->proposedValue = $proposedValue;
        $this->author = $author;
        $this->approvals = $this->initializeApprovals(...$approvers);
        $this->status = self::STATUS_PENDING;
    }

    private function initializeApprovals(string ...$approvers): array
    {
        return array_combine(
            $approvers,
            array_fill(0, count($approvers), self::STATUS_PENDING)
        );
    }

    public function accept(string $approver, ChangeOpinionPolicy $changeOpinionPolicy): self
    {
        $this->validateApprover($approver);

        return $this->changeStatus($approver, self::STATUS_ACCEPTED, $changeOpinionPolicy);
    }

    public function reject(string $approver, ChangeOpinionPolicy $changeOpinionPolicy): self
    {
        $this->validateApprover($approver);

        return $this->changeStatus($approver, self::STATUS_REJECTED, $changeOpinionPolicy);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    private function recalculateStatus(): void
    {
        $temporaryStatus = self::STATUS_ACCEPTED;

        foreach ($this->approvals as $approver => $status) {
            if ($status === self::STATUS_REJECTED) {
                $this->status = self::STATUS_REJECTED;
                return;
            } elseif ($status === self::STATUS_PENDING) {
                $temporaryStatus = self::STATUS_PENDING;
            }
        }

        $this->status = $temporaryStatus;
    }

    public function getValueName(): string
    {
        return $this->valueName;
    }

    public function getProposedValue(): Value
    {
        return $this->proposedValue;
    }

    private function changeStatus($approver, $targetStatus, $changeOpinionPolicy)
    {
        if (
            $this->approvals[$approver] !== self::STATUS_PENDING
            && !$changeOpinionPolicy->canChangeOpinion()
        ) {
            throw new NotAllowedToChangeOpinion($this->approvals[$approver], $targetStatus);
        }

        $this->approvals[$approver] = $targetStatus;

        $this->recalculateStatus();

        return $this;
    }

    /**
     * @param string $approver
     */
    private function validateApprover(string $approver): void
    {
        if (!in_array($approver, array_keys($this->approvals))) {
            throw new ApproverNotAllowed();
        }
    }
}
