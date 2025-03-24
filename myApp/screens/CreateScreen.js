import React, { useState } from 'react';
import { View, Text, TextInput, Button } from 'react-native';
import axios from 'axios';

export default function CreateScreen({ navigation }) {
  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [difficulty, setDifficulty] = useState('1');
  const username = 'someUser'; // or from your auth context

  const handleCreate = () => {
    axios.post('http://10.0.2.2/myApp/api_trivia.php', {
      username,
      trivia_question: question,
      trivia_answer: answer,
      difficulty: parseInt(difficulty, 10)
    })
    .then(response => {
      console.log(response.data);
      // maybe navigate back to Home
      navigation.goBack();
    })
    .catch(err => console.error(err));
  };

  return (
    <View>
      <Text>New Trivia</Text>
      <TextInput
        placeholder="Question"
        value={question}
        onChangeText={setQuestion}
      />
      <TextInput
        placeholder="Answer"
        value={answer}
        onChangeText={setAnswer}
      />
      <TextInput
        placeholder="Difficulty"
        value={difficulty}
        onChangeText={setDifficulty}
        keyboardType="numeric"
      />
      <Button title="Create" onPress={handleCreate} />
    </View>
  );
}
