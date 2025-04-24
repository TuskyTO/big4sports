import React, { useEffect, useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  Button,
  Alert
} from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';

export default function HomeScreen({ navigation, route }) {
  const { loggedInUser } = route.params || {};
  const [triviaList, setTriviaList] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!loggedInUser) {
      navigation.navigate('Login');
    }
  }, [loggedInUser]);

  useFocusEffect(
    useCallback(() => {
      fetchTrivia();
    }, [])
  );

  const fetchTrivia = () => {
    axios.get(`http://10.0.2.2/big4sports/backend/api_trivia.php?username=${loggedInUser}`)
      .then(response => {
        setTriviaList(response.data);
      })
      .catch(err => {
        console.error(err);
        setError(err.message);
      });
  };

  const handleDelete = (id) => {
    Alert.alert(
      'Delete Trivia',
      'Are you sure you want to delete this item?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: () => {
            axios.delete(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}`)
              .then(res => {
                setTriviaList(prev => prev.filter(item => item.id != id));
              })
              .catch(err => console.error(err));
          }
        }
      ]
    );
  };

  const renderItem = ({ item }) => (
    <View style={styles.itemContainer}>
      <Text>Question: {item.trivia_question}</Text>
      <Text>Difficulty: {item.difficulty}</Text>
      <Text>Created By: {item.username}</Text>

      {item.trivia_answer ? (
  <Text>Answer: {item.trivia_answer}</Text>
) : (
  <Button
    title="Guess Answer"
    onPress={() => navigation.navigate('ReadTrivia', { id: item.id, loggedInUser })}
  />
)}


      <View style={styles.buttonRow}>
        <Button
          title="Read"
          onPress={() => navigation.navigate('ReadTrivia', { id: item.id, loggedInUser })} // ✅ fixed this
        />
        {loggedInUser === item.username && (
          <>
            <Button
              title="Update"
              onPress={() => navigation.navigate('UpdateTrivia', { id: item.id, loggedInUser })}
            />
            <Button
              title="Delete"
              color="red"
              onPress={() => handleDelete(item.id)}
            />
          </>
        )}
      </View>
    </View>
  );

  return (
    <View style={styles.container}>
      <Text style={styles.title}>All Trivia</Text>

      {error ? (
        <Text style={{ color: 'red', marginBottom: 10 }}>{error}</Text>
      ) : null}

      <Button
        title="Create New Trivia"
        onPress={() => navigation.navigate('CreateTrivia', { loggedInUser })}
      />

<Button
  title="Reset My Guesses"
  color="orange"
  onPress={() => {
    Alert.alert(
      "Reset All Guesses",
      "Are you sure you want to reset all your guesses?",
      [
        { text: "Cancel", style: "cancel" },
        {
          text: "Reset",
          style: "destructive",
          onPress: () => {
            axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
              action: 'reset_guesses',
              username: loggedInUser
            })
            .then((res) => {
              alert(res.data.message);
              fetchTrivia();  // Refresh list
            })
            .catch((err) => {
              alert("Error resetting guesses");
              console.error(err);
            });
          }
        }
      ]
    );
  }}
/>

      <FlatList
        data={triviaList}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={{ paddingVertical: 10 }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 16,
    flex: 1,
    backgroundColor: '#f2f2f2',
  },
  title: {
    fontSize: 22,
    marginVertical: 10,
    fontWeight: 'bold'
  },
  itemContainer: {
    padding: 12,
    marginBottom: 8,
    backgroundColor: '#fff',
    borderRadius: 4
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10
  }
});
