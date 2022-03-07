<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $availableAt;

    /**
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="Question")
     */
    private $answers;

    /**
     * @ORM\OneToOne(targetEntity=Reward::class, mappedBy="question", cascade={"persist", "remove"})
     */
    private $reward;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isResolved;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getQuestion();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvailableAt(): ?\DateTimeImmutable
    {
        return $this->availableAt;
    }

    public function setAvailableAt(\DateTimeImmutable $availableAt): self
    {
        $this->availableAt = $availableAt;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    public function getReward(): ?Reward
    {
        return $this->reward;
    }

    public function setReward(Reward $reward): self
    {
        // set the owning side of the relation if necessary
        if ($reward->getQuestion() !== $this) {
            $reward->setQuestion($this);
        }

        $this->reward = $reward;

        return $this;
    }

    public function getIsResolved(): ?bool
    {
        return $this->isResolved;
    }

    public function setIsResolved(bool $isResolved): self
    {
        $this->isResolved = $isResolved;

        return $this;
    }
}
